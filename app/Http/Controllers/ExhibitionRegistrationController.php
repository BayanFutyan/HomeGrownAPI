<?php

namespace App\Http\Controllers;

use App\Models\Exhibition;
use App\Models\ExhibitionRegistration;
use App\Models\User;
use App\Enums\UserRoleEnum;
use Illuminate\Http\Request;
use App\Models\Follower;
use App\Models\Rating;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use App\Models\ExhibitionInterest;
use App\Services\FirebaseNotificationService;
use App\Models\Notification;

class ExhibitionRegistrationController extends Controller
{

    public function inviteArtisan($exhibitionId, Request $request)
    {
        $validated = $request->validate([
            'seller_id' => 'required|exists:users,id',
        ]);

        $exhibition = Exhibition::find($exhibitionId);

        if (!$exhibition) {
            return response()->json([
                'message' => 'Exhibition not found'
            ], 404);
        }

        $seller = User::find($validated['seller_id']);

        $sellerRole = $seller->role instanceof UserRoleEnum
            ? $seller->role->value
            : $seller->role;

        if ($sellerRole !== UserRoleEnum::ARTISAN->value) {
            return response()->json([
                'message' => 'Only artisans can be invited to exhibitions.'
            ], 403);
        }

   $existingRegistration = ExhibitionRegistration::where('exhibition_id', $exhibition->id)
    ->where('seller_id', $seller->id)
    ->first();

if ($existingRegistration) {
    if ($existingRegistration->type === 'request') {
        return response()->json([
            'message' => 'This artisan has already requested to join this exhibition. Please review the request from the Requests page.',
            'reason' => 'already_requested',
        ], 409);
    }

    if ($existingRegistration->type === 'invitation') {
        return response()->json([
            'message' => 'This artisan has already been invited to this exhibition.',
            'reason' => 'already_invited',
        ], 409);
    }

    return response()->json([
        'message' => 'This artisan is already registered for this exhibition.',
        'reason' => 'already_registered',
    ], 409);
}

        $invitation = ExhibitionRegistration::create([
            'exhibition_id' => $exhibition->id,
            'seller_id' => $seller->id,
            'type' => 'invitation',
            'status' => 'pending',
        ]);

        // ✅ إضافة إشعار للفنان المدعو
        $owner = User::find($exhibition->owner_id ?? $exhibition->user_id); // تأكد من اسم العمود

        if ($owner) {
            $title = '📨 Invitation to Exhibition';
            $body = $owner->name . ' invited you to join exhibition "' . $exhibition->title . '"';

            $data = [
                'type' => 'exhibition_invitation',
                'exhibition_id' => $exhibition->id,
                'registration_id' => $invitation->id,
                'owner_id' => $owner->id,
                'click_action' => 'exhibition_invitations_page',
            ];

            Notification::create([
                'user_id' => $seller->id,
                'title' => $title,
                'body' => $body,
                'type' => 'exhibition_invitation',
                'data' => $data,
                'is_read' => false,
            ]);

            $tokens = $seller->fcmTokens()->pluck('token')->toArray();
            if (!empty($tokens)) {
                $firebaseService = new FirebaseNotificationService();
                $firebaseService->send($tokens, $title, $body, $data);
            }
        }

        return response()->json([
            'message' => 'Artisan invited successfully',
            'data' => $invitation
        ], 201);
    }

    public function getExhibitionRegistrations($exhibitionId)
    {
        $exhibition = Exhibition::find($exhibitionId);

        if (!$exhibition) {
            return response()->json(['message' => 'Exhibition not found'], 404);
        }

        $registrations = ExhibitionRegistration::with('seller')
            ->where('exhibition_id', $exhibitionId)
            ->latest()
            ->get();

        $result = [];
        foreach ($registrations as $reg) {
            $seller = $reg->seller;
            $sellerId = $seller->id;

            $followersCount = Follower::where('following_id', $sellerId)->count();
            $averageRating = Rating::where('artisan_id', $sellerId)->avg('rating');
            $productsCount = Product::where('seller_id', $sellerId)->count();

            $result[] = [
                'id' => $reg->id,
                'exhibition_id' => $reg->exhibition_id,
                'seller_id' => $reg->seller_id,
                'type' => $reg->type,
                'status' => $reg->status,
                'created_at' => $reg->created_at,
                'updated_at' => $reg->updated_at,
                'seller' => [
                    'id' => $seller->id,
                    'name' => $seller->name,
                    'role' => $seller->role,
                    'profile_image' => $seller->profile_image,
                    'bio' => $seller->bio,
                    'followers_count' => $followersCount,
                    'average_rating' => $averageRating ? (float) number_format($averageRating, 1) : null,
                    'products_count' => $productsCount,
                ],
                'exhibition' => [
                    'id' => $reg->exhibition->id,
                    'title' => $reg->exhibition->title,
                    'location' => $reg->exhibition->location,
                    'date' => $reg->exhibition->date,
                    'image' => $reg->exhibition->image,
                ],
            ];
        }

        return response()->json(['data' => $result]);
    }

    public function updateStatus($registrationId, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
            'actor_id' => 'required|exists:users,id',
        ]);

        $registration = ExhibitionRegistration::with('exhibition')->find($registrationId);

        if (!$registration) {
            return response()->json([
                'message' => 'Registration not found'
            ], 404);
        }

        $actor = User::find($validated['actor_id']);

        $actorRole = $actor->role instanceof UserRoleEnum
            ? $actor->role->value
            : $actor->role;

        if ($registration->type === 'request') {
            if ($actorRole !== UserRoleEnum::EXHIBITION_OWNER->value) {
                return response()->json([
                    'message' => 'Only exhibition owners can accept or reject registration requests.'
                ], 403);
            }

            if ($registration->exhibition->owner_id !== $actor->id) {
                return response()->json([
                    'message' => 'You are not the owner of this exhibition.'
                ], 403);
            }
        }

        if ($registration->type === 'invitation') {
            if ($actorRole !== UserRoleEnum::ARTISAN->value) {
                return response()->json([
                    'message' => 'Only artisans can accept or reject invitations.'
                ], 403);
            }

            if ($registration->seller_id !== $actor->id) {
                return response()->json([
                    'message' => 'This invitation does not belong to you.'
                ], 403);
            }
        }

        $oldStatus = $registration->status;
        $registration->update([
            'status' => $validated['status']
        ]);



        // ✅ إضافة إشعار عند قبول أو رفض الطلب
        if ($oldStatus !== $validated['status'] && $validated['status'] === 'accepted') {
            if ($registration->type === 'request') {
                // الفنان قبل طلب الانضمام -> إشعار للفنان
                $artisan = User::find($registration->seller_id);
                $owner = $actor; // صاحب المعرض هو الذي قبل الطلب

                if ($artisan) {
                    $title = '✅ Join Request Accepted';
                    $body = $owner->name . ' accepted your request to join exhibition "' . $registration->exhibition->title . '"';

                    $data = [
                        'type' => 'exhibition_join_accepted',
                        'exhibition_id' => $registration->exhibition_id,
                        'registration_id' => $registration->id,
                        'owner_id' => $owner->id,
                        'actor_id' => $owner->id,
                        'click_action' => 'exhibition_page',
                    ];

                    Notification::create([
                        'user_id' => $artisan->id,
                        'title' => $title,
                        'body' => $body,
                        'type' => 'exhibition_join_accepted',
                        'data' => $data,
                        'is_read' => false,
                    ]);

                    $tokens = $artisan->fcmTokens()->pluck('token')->toArray();
                    if (!empty($tokens)) {
                        $firebaseService = new FirebaseNotificationService();
                        $firebaseService->send($tokens, $title, $body, $data);
                    }
                }
            } elseif ($registration->type === 'invitation') {
                // الفنان قبل الدعوة -> إشعار لصاحب المعرض
                $artisan = $actor; // الفنان هو الذي قبل الدعوة
                $owner = User::find($registration->exhibition->owner_id ?? $registration->exhibition->user_id);

                if ($owner) {
                    $title = '✅ Invitation Accepted';
                    $body = $artisan->name . ' accepted your invitation to join exhibition "' . $registration->exhibition->title . '"';

                    $data = [
                        'type' => 'exhibition_invitation_accepted',
                        'exhibition_id' => $registration->exhibition_id,
                        'registration_id' => $registration->id,
                        'artisan_id' => $artisan->id,
                        'click_action' => 'exhibition_page',
                    ];

                    Notification::create([
                        'user_id' => $owner->id,
                        'title' => $title,
                        'body' => $body,
                        'type' => 'exhibition_invitation_accepted',
                        'data' => $data,
                        'is_read' => false,
                    ]);

                    $tokens = $owner->fcmTokens()->pluck('token')->toArray();
                    if (!empty($tokens)) {
                        $firebaseService = new FirebaseNotificationService();
                        $firebaseService->send($tokens, $title, $body, $data);
                    }
                }
            }
        } elseif ($oldStatus !== $validated['status'] && $validated['status'] === 'rejected') {
            // إشعار بالرفض
            $notifyUser = null;
            if ($registration->type === 'request') {
                $notifyUser = User::find($registration->seller_id);
                $message = 'Your request to join exhibition "' . $registration->exhibition->title . '" was rejected';
            } else {
                $notifyUser = User::find($registration->exhibition->owner_id ?? $registration->exhibition->user_id);
                $message = $registration->seller->name . ' rejected your invitation to join exhibition "' . $registration->exhibition->title . '"';
            }

            if ($notifyUser) {
                $title = '❌ Request Rejected';
                $body = $message;

                $data = [
                    'type' => 'exhibition_request_rejected',
                    'exhibition_id' => $registration->exhibition_id,
                    'registration_id' => $registration->id,
                    'owner_id' => $actor->id,
                    'actor_id' => $actor->id,
                    'click_action' => 'exhibition_page',
                ];
                Notification::create([
                    'user_id' => $notifyUser->id,
                    'title' => $title,
                    'body' => $body,
                    'type' => 'exhibition_request_rejected',
                    'data' => $data,
                    'is_read' => false,
                ]);

                $tokens = $notifyUser->fcmTokens()->pluck('token')->toArray();
                if (!empty($tokens)) {
                    $firebaseService = new FirebaseNotificationService();
                    $firebaseService->send($tokens, $title, $body, $data);
                }
            }
        }

        return response()->json([
            'message' => 'Status updated successfully',
            'data' => $registration
        ]);
    }

    public function getOwnerRegistrations()
    {
        $ownerId = auth()->id();

        $registrations = ExhibitionRegistration::with([
            'seller:id,name,role,profile_image',
            'exhibition'
        ])->whereHas('exhibition', function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        })->latest()->get();

        $result = [];
        foreach ($registrations as $reg) {
            $seller = $reg->seller;
            $sellerId = $seller->id;

            $followersCount = Follower::where('following_id', $sellerId)->count();

            Log::info("Seller ID: $sellerId, Followers Count: $followersCount");

            $result[] = [
                'id' => $reg->id,
                'exhibition_id' => $reg->exhibition_id,
                'seller_id' => $reg->seller_id,
                'type' => $reg->type,
                'status' => $reg->status,
                'created_at' => $reg->created_at,
                'updated_at' => $reg->updated_at,
                'seller' => [
                    'id' => $seller->id,
                    'name' => $seller->name,
                    'role' => $seller->role,
                    'profile_image' => $seller->profile_image,
                    'followers_count' => $followersCount,
                ],
                'exhibition' => $reg->exhibition,
            ];
        }

        return response()->json(['data' => $result]);
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(ExhibitionRegistration $exhibitionRegistration)
    {
        //
    }

    public function edit(ExhibitionRegistration $exhibitionRegistration)
    {
        //
    }

    public function update(Request $request, ExhibitionRegistration $exhibitionRegistration)
    {
        //
    }

    public function destroy(ExhibitionRegistration $exhibitionRegistration)
    {
        //
    }

    public function getArtisanRegistrations()
    {
        $artisanId = auth()->id();

        $registrations = ExhibitionRegistration::with(['exhibition.owner', 'seller'])
            ->where('seller_id', $artisanId)
            ->latest()
            ->get();

        $result = [];
        foreach ($registrations as $reg) {
            $exhibition = $reg->exhibition;
            $owner = $exhibition->owner;

            $interestsCount = ExhibitionInterest::where('exhibition_id', $exhibition->id)->count();

            $result[] = [
                'id' => $reg->id,
                'exhibition_id' => $reg->exhibition_id,
                'seller_id' => $reg->seller_id,
                'type' => $reg->type,
                'status' => $reg->status,
                'created_at' => $reg->created_at,
                'updated_at' => $reg->updated_at,
                'exhibition' => [
                    'id' => $exhibition->id,
                    'title' => $exhibition->title,
                    'description' => $exhibition->description,
                    'location' => $exhibition->location,
                    'image' => $exhibition->image,
                    'start_date' => $exhibition->start_date,
                    'end_date' => $exhibition->end_date,
                    'type' => $exhibition->type,
                    'participants_count' => $interestsCount,
                    'owner' => [
                        'id' => $owner->id,
                        'name' => $owner->name,
                    ],
                ],
                'seller' => [
                    'id' => $reg->seller->id,
                    'name' => $reg->seller->name,
                    'profile_image' => $reg->seller->profile_image,
                ],
            ];
        }

        return response()->json(['data' => $result]);
    }

    public function applyToExhibition(Request $request)
    {
        $validated = $request->validate([
            'exhibition_id' => 'required|exists:exhibitions,id',
        ]);

        $artisanId = auth()->id();
        $exhibitionId = $validated['exhibition_id'];

        $exhibition = Exhibition::find($exhibitionId);

        if (!$exhibition) {
            return response()->json(['message' => 'Exhibition not found'], 404);
        }

        if ($exhibition->type !== 'public') {
            return response()->json(['message' => 'This exhibition is not public'], 403);
        }

        $existing = ExhibitionRegistration::where('exhibition_id', $exhibitionId)
            ->where('seller_id', $artisanId)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You already applied or were invited'], 400);
        }

        $registration = ExhibitionRegistration::create([
            'exhibition_id' => $exhibitionId,
            'seller_id' => $artisanId,
            'type' => 'request',
            'status' => 'pending',
        ]);

        // ✅ كود الإشعار (هذا صحيح وشغال)
        $artisan = auth()->user();
        // تأكد من اسم العمود الصحيح (owner_id أو user_id)
        $owner = User::find($exhibition->owner_id ?? $exhibition->user_id);

        if ($owner) {
            $title = 'Exhibition Join Request';
            $body = $artisan->name . ' requested to join your exhibition "' . $exhibition->title . '"';

            $data = [
                'type' => 'exhibition_join_request',
                'exhibition_id' => $exhibition->id,
                'registration_id' => $registration->id,

                'artisan_id' => $artisan->id,
                'actor_id' => $artisan->id,

                'artisan_name' => $artisan->name,
                'exhibition_name' => $exhibition->title,
                'click_action' => 'exhibition_requests_page',
            ];

            Notification::create([
                'user_id' => $owner->id,
                'title' => $title,
                'body' => $body,
                'type' => 'exhibition_join_request',
                'data' => $data,
                'is_read' => false,
            ]);

            Log::info('Notification created for owner', [
                'owner_id' => $owner->id,
                'exhibition_id' => $exhibition->id,
                'artisan_id' => $artisan->id
            ]);

            $tokens = $owner->fcmTokens()->pluck('token')->toArray();

            if (!empty($tokens)) {
                try {
                    $firebaseService = new FirebaseNotificationService();
                    $firebaseService->send($tokens, $title, $body, $data);
                    Log::info('FCM sent successfully', ['tokens_count' => count($tokens)]);
                } catch (\Exception $e) {
                    Log::error('FCM failed: ' . $e->getMessage());
                }
            } else {
                Log::warning('No FCM tokens found for owner', ['owner_id' => $owner->id]);
            }
        } else {
            Log::error('Owner not found for exhibition', [
                'exhibition_id' => $exhibition->id,
                'owner_id' => $exhibition->owner_id ?? $exhibition->user_id ?? 'null'
            ]);
        }

        return response()->json([
            'message' => 'Application submitted successfully',
            'data' => $registration
        ], 201);
    }

    public function getArtisanUpcomingExhibitions()
    {
        $artisanId = auth()->id();

        $registrations = ExhibitionRegistration::with(['exhibition.owner', 'seller'])
            ->where('seller_id', $artisanId)
            ->where('status', 'accepted')
            ->whereHas('exhibition', function ($query) {
                $query->where('status', 'upcoming');
            })
            ->latest()
            ->get();

        return response()->json([
            'data' => $registrations
        ]);
    }



    public function artisanDiscovery(Request $request)
    {
        $ownerId = auth()->id();
        

        $search = $request->query('search');
        $status = $request->query('status', 'all');
        $selectedExhibitionId = $request->query('exhibition_id');

        if ($selectedExhibitionId) {
            $ownerExhibitionIds = Exhibition::where('owner_id', $ownerId)
                ->where('id', $selectedExhibitionId)
                ->pluck('id');
        } else {
            $ownerExhibitionIds = Exhibition::where('owner_id', $ownerId)->pluck('id');
        }

        $query = User::where('role', 'artisan')
            ->with(['products' => function ($q) {
                $q->latest()->limit(1);
            }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('bio', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhereHas('products', function ($p) use ($search) {
                        $p->where('name', 'like', "%$search%")
                            ->orWhere('category', 'like', "%$search%");
                    });
            });
        }

        $artisans = $query->get()->map(function ($artisan) use ($ownerExhibitionIds) {
            $registration = ExhibitionRegistration::with('exhibition')
                ->whereIn('exhibition_id', $ownerExhibitionIds)
                ->where('seller_id', $artisan->id)
                ->where('type', 'invitation')
                ->latest()
                ->first();

            $invitationStatus = 'not_invited';

            if ($registration) {
                if ($registration->status === 'pending') {
                    $invitationStatus = 'invitation_sent';
                } elseif ($registration->status === 'accepted') {
                    $invitationStatus = 'accepted';
                } elseif ($registration->status === 'rejected') {
                    $invitationStatus = 'declined';
                }
            }

            return [
                'id' => $artisan->id,
                'name' => $artisan->name,
                'email' => $artisan->email,
                'phone' => $artisan->phone,
                'profile_image' => $artisan->profile_image ? url('/' . $artisan->profile_image) : null,
                'address' => $artisan->address,
                'bio' => $artisan->bio,
                'role' => $artisan->role,
                'followers_count' => $artisan->followers()->count(),
                'following_count' => $artisan->following()->count(),
                'average_rating' => round($artisan->ratingsReceived()->avg('rating') ?? 0, 1),
                'ratings_count' => $artisan->ratingsReceived()->count(),
                'invitation_status' => $invitationStatus,
                'registration_id' => $registration?->id,
                'exhibition_id' => $registration?->exhibition_id,
                'exhibition_title' => $registration?->exhibition?->title,
                'products' => $artisan->products,
                'created_at' => $artisan->created_at,
                'updated_at' => $artisan->updated_at,
                'total_sales' => $artisan->products()->sum('sales_count'),
            ];
        });

        if ($status !== 'all') {
            $artisans = $artisans->where('invitation_status', $status)->values();
        }

        $stats = [
            'all' => $artisans->count(),
            'not_invited' => $artisans->where('invitation_status', 'not_invited')->count(),
            'invitation_sent' => $artisans->where('invitation_status', 'invitation_sent')->count(),
            'accepted' => $artisans->where('invitation_status', 'accepted')->count(),
            'declined' => $artisans->where('invitation_status', 'declined')->count(),
        ];

        return response()->json([
            'data' => [
                'stats' => $stats,
                'artisans' => $artisans->values(),
            ],
            'message' => 'Artisan discovery loaded successfully',
        ]);
    }
}

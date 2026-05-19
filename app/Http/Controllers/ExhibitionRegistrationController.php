<?php

namespace App\Http\Controllers;

use App\Models\Exhibition;
use App\Models\ExhibitionRegistration;
use App\Models\User;
use App\Enums\UserRoleEnum;
use Illuminate\Http\Request;
use App\Models\Follower; // ✅ أضيفي هذا في الأعلى


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

        $alreadyInvited = ExhibitionRegistration::where('exhibition_id', $exhibition->id)
            ->where('seller_id', $seller->id)
            ->exists();

        if ($alreadyInvited) {
            return response()->json([
                'message' => 'This artisan is already invited or registered for this exhibition.'
            ], 400);
        }

        $invitation = ExhibitionRegistration::create([
        'exhibition_id' => $exhibition->id,
        'seller_id' => $seller->id,
        'type' => 'invitation',
        'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Artisan invited successfully',
            'data' => $invitation
        ], 201);
    }

    public function getExhibitionRegistrations($exhibitionId)
    {
        $exhibition = Exhibition::find($exhibitionId);

        if (!$exhibition) {
            return response()->json([
                'message' => 'Exhibition not found'
            ], 404);
        }

        $registrations = ExhibitionRegistration::with('seller')
            ->where('exhibition_id', $exhibitionId)
            ->latest()
            ->get();

        return response()->json([
            'data' => $registrations
        ]);
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

        $registration->update([
            'status' => $validated['status']
        ]);

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
        
        // ✅ استعلام بسيط
        $followersCount = Follower::where('following_id', $sellerId)->count();
        
        // ✅ طباعة للتأكد
        \Log::info("Seller ID: $sellerId, Followers Count: $followersCount");
        
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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ExhibitionRegistration $exhibitionRegistration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExhibitionRegistration $exhibitionRegistration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExhibitionRegistration $exhibitionRegistration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExhibitionRegistration $exhibitionRegistration)
    {
        //
    }

    
}

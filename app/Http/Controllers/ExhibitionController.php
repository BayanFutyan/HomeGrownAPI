<?php

namespace App\Http\Controllers;

use App\Models\Exhibition;
use Illuminate\Http\Request;
use App\Enums\UserRoleEnum;
use App\Models\User;

class ExhibitionController extends Controller
{
    
    public function getByOwner($ownerId)
    {
        $owner = User::find($ownerId);

        if (!$owner) {
            return response()->json([
                'message' => 'Owner not found'
            ], 404);
        }

        $ownerRole = $owner->role instanceof UserRoleEnum
            ? $owner->role->value
            : $owner->role;

        if ($ownerRole !== UserRoleEnum::EXHIBITION_OWNER->value) {
            return response()->json([
                'message' => 'This user is not an exhibition owner.'
            ], 403);
        }

        $exhibitions = Exhibition::where('owner_id', $ownerId)
            ->latest()
            ->get();

        return response()->json([
            'data' => $exhibitions
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exhibitions = Exhibition::with('owner')->latest()->get();

        return response()->json($exhibitions);
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
    $validated = $request->validate([
        'owner_id'    => 'required|exists:users,id',
        'title'       => 'required|string|max:255',
        'description' => 'nullable|string',
        'image'       => 'nullable|image|mimes:jpg,png,jpeg,gif',
        'start_date'  => 'nullable|date',
        'end_date'    => 'nullable|date',
        'status'      => 'nullable|in:upcoming,active,ended',
        'location'    => 'nullable|string|max:255',
        'type'        => 'required|in:public,private',
    ]);

    $imagePath = null;
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        // اسم الصورة بنفس طريقة التسمية القديمة
        $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $file->move(public_path('images/exhibitions'), $filename);
        $imagePath = "images/exhibitions/$filename";
    }

    $exhibition = Exhibition::create([
        'owner_id' => $validated['owner_id'],
        'title' => $validated['title'],
        'description' => $validated['description'] ?? null,
        'image' => $imagePath,
        'start_date' => $validated['start_date'] ?? null,
        'end_date' => $validated['end_date'] ?? null,
        'status' => $validated['status'] ?? 'upcoming',
        'location' => $validated['location'] ?? null,
        'participants_count' => 0,
        'type' => $validated['type'],
    ]);

    return response()->json([
        'message' => 'Exhibition created successfully',
        'data' => $exhibition
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(Exhibition $exhibition)
    {
        $exhibition->load('owner');

        return response()->json([
            'data' => $exhibition
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exhibition $exhibition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, Exhibition $exhibition)
{
    // التحقق من الصلاحية
    if ($exhibition->owner_id != auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    
    $validated = $request->validate([
        'title'       => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
        'start_date'  => 'nullable|date',
        'end_date'    => 'nullable|date',
        'status'      => 'nullable|in:upcoming,active,ended',
        'location'    => 'nullable|string|max:255',
        'type'        => 'sometimes|in:public,private',
    ]);
    
    // ✅ معالجة الصورة (هذا هو التغيير المهم)
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('images/exhibitions'), $filename);
        $validated['image'] = "images/exhibitions/$filename";
    }
    
    $exhibition->update($validated);
    
    return response()->json([
        'message' => 'Exhibition updated successfully',
        'data' => $exhibition
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exhibition $exhibition)
    {
        $exhibition->delete();

        return response()->json([
            'message' => 'Exhibition deleted successfully'
        ]);
    }

    public function myExhibitions(Request $request)
    {
        $user = $request->user();

        $userRole = $user->role instanceof UserRoleEnum
            ? $user->role->value
            : $user->role;

        if ($userRole !== UserRoleEnum::EXHIBITION_OWNER->value) {
            return response()->json([
                'message' => 'Only exhibition owners can view their exhibitions.'
            ], 403);
        }

        $exhibitions = Exhibition::where('owner_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => $exhibitions
        ]);
    }

public function updateWithImage(Request $request, $id)
{
    $exhibition = Exhibition::findOrFail($id);
    
    if ($exhibition->owner_id != auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    
    $data = $request->validate([
        'title' => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'location' => 'sometimes|string|max:255',
        'start_date' => 'sometimes|date',
        'end_date' => 'sometimes|date|after_or_equal:start_date',
        'type' => 'sometimes|in:public,private',
    ]);
    
    // ✅ رفع الصورة
    if ($request->hasFile('image')) {
        // حذف الصورة القديمة
        if ($exhibition->image && file_exists(storage_path('app/public/' . $exhibition->image))) {
            unlink(storage_path('app/public/' . $exhibition->image));
        }
        
        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->storeAs('public/exhibitions', $imageName);
        $data['image'] = 'images/exhibitions/' . $imageName;
    }
    
    $exhibition->update($data);
    
    return response()->json([
        'message' => 'Exhibition updated successfully',
        'data' => $exhibition
    ]);
}

public function uploadImage(Request $request, $id)
{
    try {
        $exhibition = Exhibition::findOrFail($id);
        
        // التحقق من الصلاحية
        if ($exhibition->owner_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        // رفع الصورة
        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = $image->storeAs('exhibitions', $imageName, 'public');
        
        // ✅ حفظ المسار في قاعدة البيانات
        $exhibition->image = 'images/exhibitions/' . $imageName;
        $exhibition->save();
        
        return response()->json([
            'message' => 'Image uploaded successfully',
            'data' => $exhibition
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage()
        ], 500);
    }
}

}


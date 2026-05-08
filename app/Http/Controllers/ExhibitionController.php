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
        'image'       => 'nullable|string',
        'start_date'  => 'nullable|date',
        'end_date'    => 'nullable|date',
        'status'      => 'nullable|in:upcoming,active,ended',
        'location'    => 'nullable|string|max:255',
        'type' => 'required|in:public,private',
    ]);

    $owner = User::find($validated['owner_id']);

    $ownerRole = $owner->role instanceof UserRoleEnum
        ? $owner->role->value
        : $owner->role;

    if ($ownerRole !== UserRoleEnum::EXHIBITION_OWNER->value) {
        return response()->json([
            'message' => 'Only exhibition owners can create exhibitions.'
        ], 403);
    }

    $exhibition = Exhibition::create([
        'owner_id' => $owner->id,
        'title' => $validated['title'],
        'description' => $validated['description'] ?? null,
        'image' => $validated['image'] ?? null,
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
        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|string',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'status'      => 'nullable|in:upcoming,active,ended',
            'location'    => 'nullable|string|max:255',
        ]);

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
}

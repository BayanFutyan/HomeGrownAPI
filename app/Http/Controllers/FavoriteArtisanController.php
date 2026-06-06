<?php

namespace App\Http\Controllers;

use App\Models\FavoriteArtisan;
use App\Models\User;
use Illuminate\Http\Request;

class FavoriteArtisanController extends Controller
{
    public function index()
    {
        $ownerId = auth()->id();

        $favorites = FavoriteArtisan::with(['artisan.products'])
            ->where('owner_id', $ownerId)
            ->latest()
            ->get()
            ->map(function ($favorite) {
                return $favorite->artisan;
            });

        return response()->json([
            'data' => $favorites,
        ]);
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'artisan_id' => 'required|exists:users,id',
        ]);

        $ownerId = auth()->id();
        $artisanId = $request->artisan_id;

        $artisan = User::where('id', $artisanId)
            ->where('role', 'artisan')
            ->first();

        if (!$artisan) {
            return response()->json([
                'message' => 'Selected user is not an artisan',
            ], 422);
        }

        $favorite = FavoriteArtisan::where('owner_id', $ownerId)
            ->where('artisan_id', $artisanId)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return response()->json([
                'message' => 'Artisan removed from favorites',
                'is_favorite' => false,
            ]);
        }

        FavoriteArtisan::create([
            'owner_id' => $ownerId,
            'artisan_id' => $artisanId,
        ]);

        return response()->json([
            'message' => 'Artisan added to favorites',
            'is_favorite' => true,
        ]);
    }
}
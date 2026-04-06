<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;

class OfferController extends Controller
{
    /**
     * Get all offers
     */
    public function index(): JsonResponse
    {
        $offers = Offer::with('product')->get();

        return response()->json([
            'data' => $offers,
            'message' => 'Offers retrieved successfully'
        ]);
    }

    /**
     * Get a specific offer
     */
    public function show($id): JsonResponse
    {
        $offer = Offer::with('product')->find($id);

        if (!$offer) {
            return response()->json(['message' => 'Offer not found'], 404);
        }

        return response()->json([
            'data' => $offer,
            'message' => 'Offer retrieved successfully'
        ]);
    }
}
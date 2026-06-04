<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductDetail;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductDetailController extends Controller
{
    /**
     * إضافة تفصيل جديد لمنتج (بدون استخدام artisan/products)
     */
    public function store(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'detail_name' => 'required|string|max:255',
            'detail_value' => 'required|string',
        ]);

        $product = Product::find($request->product_id);

        // تأكد أن المستخدم هو صاحب المنتج
        if ($product->seller_id !== $user->id) {
            return response()->json(['message' => 'You can only add details to your own products'], 403);
        }

        $detail = ProductDetail::create([
            'product_id' => $request->product_id,
            'detail_name' => $request->detail_name,
            'detail_value' => $request->detail_value,
        ]);

        return response()->json([
            'message' => 'Detail added successfully',
            'data' => $detail
        ], 201);
    }

    /**
     * Delete a product detail
     */
    public function destroy($id): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        $detail = ProductDetail::find($id);

        if (!$detail) {
            return response()->json(['message' => 'Detail not found'], 404);
        }

        $product = $detail->product;

        if ($product->seller_id !== $user->id) {
            return response()->json(['message' => 'You can only delete details of your own products'], 403);
        }

        $detail->delete();

        return response()->json([
            'message' => 'Detail deleted successfully'
        ]);
    }
}
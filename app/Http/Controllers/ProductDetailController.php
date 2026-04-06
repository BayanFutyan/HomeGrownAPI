<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProductDetailController extends Controller
{
    /**
     * Delete a product detail
     */
    public function destroy($id): JsonResponse
    {
        // مؤقتاً للاختبار: استخدم المستخدم رقم 1
        $user = \App\Models\User::find(1);
        
        // $user = Auth::user();  // علق هذا السطر مؤقتاً
        
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
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Save;
use App\Models\Follower;
use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Rating;
use App\Models\Offer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RecommendationController extends Controller
{
    public function getRecommendations($userId, Request $request)
{
    try {

        // =========================
        // USER INTERACTIONS
        // =========================
        $likedProducts = Like::where('user_id', $userId)
            ->where('likeable_type', Product::class)
            ->pluck('likeable_id')
            ->toArray();

        $savedProducts = Save::where('user_id', $userId)
            ->where('saveable_type', Product::class)
            ->pluck('saveable_id')
            ->toArray();

        $cartIds = Cart::where('customer_id', $userId)
            ->pluck('id')
            ->toArray();

        $cartProducts = CartItem::whereIn('cart_id', $cartIds)
            ->pluck('product_id')
            ->toArray();

        $userProductIds = array_unique(array_merge(
            $likedProducts,
            $savedProducts,
            $cartProducts
        ));

        $userSellerIds = Product::whereIn('id', $userProductIds)
            ->pluck('seller_id')
            ->unique()
            ->toArray();

        // =========================
        // USER CATEGORY PROFILE
        // =========================
        $userCategories = Like::where('user_id', $userId)
            ->where('likeable_type', Product::class)
            ->join('products', 'products.id', '=', 'likes.likeable_id')
            ->select('products.category')
            ->get()
            ->groupBy('category')
            ->map->count()
            ->toArray();

        // =========================
        // PRODUCTS
        // =========================
        $filter = $request->query('filter', 'explore');

        $productsQuery = Product::with([
            'seller',
            'likes',
            'comments',
            'offer'
        ]);

        if ($filter === 'offer') {
            $productsQuery->where('is_sale', 1)
                ->whereHas('offer', function ($q) {
                    $q->whereDate('start_date', '<=', now())
                      ->whereDate('end_date', '>=', now());
                })
                ->orderByDesc('sales_count');
        }

        $products = $productsQuery->get();

        // =========================
        // GROUP BY SELLER
        // =========================
        $sellers = [];

        foreach ($products as $product) {

            if (!$product) continue;

            $sellerId = $product->seller_id;

            if (!isset($sellers[$sellerId])) {
                $sellers[$sellerId] = [
                    'seller_id' => $sellerId,
                    'products' => [],
                    'index' => 0,
                    'score' => 0,
                ];
            }

            $sellers[$sellerId]['products'][] = $product;
        }

        // =========================
        // SCORE CALCULATION
        // =========================
        foreach ($sellers as $sellerId => &$sellerData) {

            $score = 0;

            $productIds = collect($sellerData['products'])->pluck('id');

            $score += Like::whereIn('likeable_id', $productIds)
                ->where('likeable_type', Product::class)
                ->count() * 2;

            $score += Save::whereIn('saveable_id', $productIds)
                ->where('saveable_type', Product::class)
                ->count() * 3;

            $score += Comment::whereIn('commentable_id', $productIds)
                ->where('commentable_type', Product::class)
                ->count();

            $score += CartItem::whereIn('product_id', $productIds)->count() * 4;

            $score += Follower::where('following_id', $sellerId)->count() * 1.5;

            $score += (Rating::where('artisan_id', $sellerId)->avg('rating') ?? 0) * 0.5;

            $newProducts = collect($sellerData['products'])
                ->filter(
                    fn($p) =>
                    $p->created_at &&
                        $p->created_at->diffInDays(now()) <= 7
                )
                ->count();

            $score += $newProducts * 0.5;

            if (in_array($sellerId, $userSellerIds)) {
                $score += 10;
            }

            // category boost
            $interestBoost = 0;

            foreach ($sellerData['products'] as $p) {
                if (isset($userCategories[$p->category])) {
                    $interestBoost += $userCategories[$p->category] * 2;
                }
            }

            $score += $interestBoost;

            $sellerData['score'] = $score;
        }

        // =========================
        // SORT SELLERS
        // =========================
        usort($sellers, fn($a, $b) => $b['score'] <=> $a['score']);

        // =========================
        // BUILD FEED
        // =========================
        $feed = [];
        $seen = [];

        $hasMore = true;

        while ($hasMore) {

            $hasMore = false;

            foreach ($sellers as &$seller) {

                if (!isset($seller['products'][$seller['index']])) {
                    continue;
                }

                if (count($feed) >= 60) break;

                $product = $seller['products'][$seller['index']];
                $seller['index']++;

                if (in_array($product->id, $seen)) {
                    continue;
                }

                $seen[] = $product->id;

                $feed[] = [
                    'id' => $product->id,
                    'seller_id' => $product->seller_id,
                    'name' => $product->name,
                    'description' => $product->description,  // ✅ تم الإضافة
                    'details' => $product->details,          // ✅ تم الإضافة
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'image' => $product->image,
                    'category' => $product->category,
                    'offer' => $product->offer,
                    'is_sale' => $product->is_sale,
                    'likes_count' => $product->likes->count(),
                    'comments_count' => $product->comments->count(),
                    'sales_count' => $product->sales_count,
                    'seller_score' => round($seller['score'], 2),
                    'seller' => $product->seller,
                ];

                $hasMore = true;
            }
        }

        return response()->json([
            'data' => $feed
        ]);
    } catch (\Throwable $e) {

        Log::error('Recommendation Error', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
}
}

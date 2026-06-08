<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Exhibition;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\Follower;
use App\Models\Rating;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function overview()
    {
        return response()->json([
            'total_customers' => User::where('role', 'user')->count(),
            'total_artisans' => User::where('role', 'artisan')->count(),
            'total_exhibition_owners' => User::where('role', 'exhibition_owner')->count(),

            'products_last_month' => Product::whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ])->count(),

            'exhibitions_last_month' => Exhibition::whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ])->count(),

            'orders_last_month' => Order::whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ])->count(),

            'new_users_last_month' => User::where('role', '!=', 'admin')
                ->whereBetween('created_at', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth(),
                ])
                ->count(),
        ]);
    }

    private function monthlyCounts($model, $months = 5, $excludeAdmin = false)
    {
        $labels = [];
        $values = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);

            $query = $model::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);

            if ($excludeAdmin && $model === User::class) {
                $query->where('role', '!=', 'admin');
            }

            $labels[] = $date->format('M');
            $values[] = $query->count();
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    public function growth()
    {
        $users = $this->monthlyCounts(User::class, 5, true);
        $products = $this->monthlyCounts(Product::class, 5);
        $orders = $this->monthlyCounts(Order::class, 5);
        $exhibitions = $this->monthlyCounts(Exhibition::class, 5);

        return response()->json([
            'labels' => $users['labels'],
            'users' => $users['values'],
            'products' => $products['values'],
            'orders' => $orders['values'],
            'exhibitions' => $exhibitions['values'],
        ]);
    }

    public function insights()
    {
        $mostLikedProduct = Product::orderByDesc('likes_count')->first();

        $mostActiveArtisan = User::where('role', 'artisan')
            ->withCount('products')
            ->orderByDesc('products_count')
            ->first();

        $mostActiveExhibition = Exhibition::all()
            ->sortByDesc(function ($exhibition) {
                return $exhibition->participants_count;
            })
            ->first();

        return response()->json([
            'most_liked_product' => [
                'name' => $mostLikedProduct?->name ?? 'No product yet',
                'likes' => $mostLikedProduct?->likes_count ?? 0,
            ],

            'most_active_artisan' => [
                'name' => $mostActiveArtisan?->name ?? 'No artisan yet',
                'products' => $mostActiveArtisan?->products_count ?? 0,
            ],

            'most_active_exhibition' => [
                'name' => $mostActiveExhibition?->title ?? 'No exhibition yet',
                'participants' => $mostActiveExhibition?->participants_count ?? 0,
            ],
        ]);
    }

    public function topRankings()
    {
        $topProducts = Product::query()
            ->select('id', 'name', 'seller_id', 'sales_count', 'likes_count', 'image')
            ->with('seller:id,name')
            ->orderByDesc('sales_count')
            ->orderByDesc('likes_count')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => (int) $product->id,  // ✅ تحويل إلى int
                    'name' => (string) $product->name,
                    'subtitle' => (string) ($product->seller?->name ?? 'Unknown artisan'),
                    'sales' => (int) ($product->sales_count ?? 0),
                    'likes' => (int) ($product->likes_count ?? 0),
                    'image' => $product->image ? asset($product->image) : null,
                ];
            });

        $topArtisans = User::where('role', 'artisan')
            ->withCount([
                'products',
                'followers',
            ])
            ->withAvg('ratingsReceived', 'rating')
            ->get()
            ->map(function ($artisan) {
                $salesCount = (int) Product::where('seller_id', $artisan->id)->sum('sales_count');
                $rating = round($artisan->ratings_received_avg_rating ?? 0, 1);

                $score = ($artisan->followers_count * 2) +
                    ($rating * 10) +
                    ($salesCount * 3) +
                    ($artisan->products_count * 1);

                return [
                    'name' => (string) $artisan->name,
                    'followers' => (int) $artisan->followers_count,
                    'rating' => (float) $rating,
                    'sales' => $salesCount,
                    'products' => (int) $artisan->products_count,
                    'score' => (int) $score,
                    'profile_image' => $artisan->profile_image ? asset($artisan->profile_image) : null,
                ];
            })
            ->sortByDesc('score')
            ->take(10)
            ->values();

        $topExhibitions = Exhibition::with('owner:id,name,profile_image')
            ->get()
            ->map(function ($exhibition) {
                return [
                    'name' => (string) $exhibition->title,
                    'subtitle' => (string) ($exhibition->owner?->name ?? 'Unknown owner'),
                    'interested_users' => (int) $exhibition->interests()->count(),
                    'owner_profile_image' => $exhibition->owner?->profile_image ? asset($exhibition->owner->profile_image) : null,
                ];
            })
            ->sortByDesc('interested_users')
            ->take(10)
            ->values();

        return response()->json([
            'top_products' => $topProducts,
            'top_artisans' => $topArtisans,
            'top_exhibitions' => $topExhibitions,
        ]);
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Exhibition;
use App\Models\Order;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;


class AdminDashboardController extends Controller
{
    // ===== Overview Data =====
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
                ])->count(),
        ]);
    }

    // ===== Monthly Growth =====
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

        return ['labels' => $labels, 'values' => $values];
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

    // ===== Top Rankings for PDF =====
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
                    'id' => (int) $product->id,
                    'name' => (string) $product->name,
                    'subtitle' => (string) ($product->seller?->name ?? 'Unknown artisan'),
                    'sales' => (int) ($product->sales_count ?? 0),
                    'likes' => (int) ($product->likes_count ?? 0),
                    'image' => $product->image ? asset($product->image) : null,
                ];
            });

        $topArtisans = User::where('role', 'artisan')
            ->withCount(['products', 'followers'])
            ->withAvg('ratingsReceived', 'rating')
            ->get()
            ->map(function ($artisan) {
                $salesCount = (int) Product::where('seller_id', $artisan->id)->sum('sales_count');
                $rating = round($artisan->ratings_received_avg_rating ?? 0, 1);
                $score = ($artisan->followers_count * 2) + ($rating * 10) + ($salesCount * 3) + ($artisan->products_count * 1);

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

    // ===== Download PDF Report =====
    public function downloadReport(Request $request)
    {
        $year = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $lastCompleteMonth = $currentMonth - 1; // آخر شهر كامل
        $month = $request->query('month', $lastCompleteMonth);

        // ===== Overview =====
        $overview = [
            'total_customers' => User::where('role', 'user')->count(),
            'total_artisans' => User::where('role', 'artisan')->count(),
            'total_exhibition_owners' => User::where('role', 'exhibition_owner')->count(),
            'products_last_month' => Product::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count(),
            'exhibitions_last_month' => Exhibition::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count(),
            'orders_last_month' => Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count(),
            'new_users_last_month' => User::where('role', '!=', 'admin')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count(),
        ];

        // ===== Growth =====
        $growth = [
            'labels' => [],
            'users' => [],
            'products' => [],
            'orders' => [],
            'exhibitions' => [],
        ];

        for ($m = 1; $m <= $month; $m++) {

            $growth['labels'][] = Carbon::create($year, $m, 1)->format('M');

            $growth['users'][] = User::where('role', '!=', 'admin')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->count();

            $growth['products'][] = Product::whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->count();

            $growth['orders'][] = Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->count();

            $growth['exhibitions'][] = Exhibition::whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->count();
        }

        // ===== Top Rankings =====
        $topProducts = Product::query()
            ->select('id', 'name', 'seller_id', 'sales_count', 'likes_count', 'image')
            ->with('seller:id,name')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderByDesc('sales_count')
            ->orderByDesc('likes_count')
            ->limit(10)
            ->get()
            ->map(fn($product) => [
                'id' => (int)$product->id,
                'name' => (string)$product->name,
                'subtitle' => (string)($product->seller?->name ?? 'Unknown artisan'),
                'sales' => (int)($product->sales_count ?? 0),
                'likes' => (int)($product->likes_count ?? 0),
                'image' => $product->image ? asset($product->image) : null,
            ]);

        $topArtisans = User::where('role', 'artisan')
            ->withCount(['products', 'followers'])
            ->withAvg('ratingsReceived', 'rating')
            ->get()
            ->map(function ($artisan) {
                $salesCount = (int) Product::where('seller_id', $artisan->id)->sum('sales_count');
                $rating = round($artisan->ratings_received_avg_rating ?? 0, 1);
                $score = ($artisan->followers_count * 2) + ($rating * 10) + ($salesCount * 3) + ($artisan->products_count * 1);
                return [
                    'name' => (string)$artisan->name,
                    'followers' => (int)$artisan->followers_count,
                    'rating' => (float)$rating,
                    'sales' => $salesCount,
                    'products' => (int)$artisan->products_count,
                    'score' => (int)$score,
                    'profile_image' => $artisan->profile_image ? asset($artisan->profile_image) : null,
                ];
            })
            ->sortByDesc('score')->take(10)->values();

        $topExhibitions = Exhibition::with('owner:id,name,profile_image')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get()
            ->map(fn($exhibition) => [
                'name' => (string)$exhibition->title,
                'subtitle' => (string)($exhibition->owner?->name ?? 'Unknown owner'),
                'interested_users' => (int)$exhibition->interests()->count(),
                'owner_profile_image' => $exhibition->owner?->profile_image ? asset($exhibition->owner->profile_image) : null,
            ])
            ->sortByDesc('interested_users')->take(10)->values();

        $topRankings = [
            'top_products' => $topProducts,
            'top_artisans' => $topArtisans,
            'top_exhibitions' => $topExhibitions,
        ];

        // ===== Generate PDF =====
        $pdf = Pdf::loadView('admin.reports.monthly', [
            'overview' => $overview,
            'growth' => $growth,
            'topRankings' => $topRankings,
            'month' => $month,
            'year' => $year,
        ]);

        return $pdf->download("HomeGrown_Monthly_Report_{$month}_{$year}.pdf");
    }
}
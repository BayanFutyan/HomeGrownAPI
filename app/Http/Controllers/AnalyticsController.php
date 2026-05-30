<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AnalyticsController extends Controller
{
    public function index()
    {
        $sellerId = Auth::id();
        $user = User::find($sellerId);

        $totalSales = Order::where('seller_id', $sellerId)
            ->where('status', 'delivered')
            ->sum('total_amount');

        $totalOrders = Order::where('seller_id', $sellerId)
            ->count();

        $totalLikes = Product::where('seller_id', $sellerId)
            ->sum('likes_count');


        $followers = $user ? $user->followers()->count() : 0;

        $salesOverTime = Order::where('seller_id', $sellerId)
            ->where('status', 'delivered')
            ->whereDate('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as date"),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topProducts = Product::where('seller_id', $sellerId)
            ->orderByDesc('sales_count')
            ->take(5)
            ->get([
                'id',
                'name',
                'image',
                'sales_count'
            ]);

        $ordersStatus = [
            'pending' => Order::where('seller_id', $sellerId)
                ->where('status', 'pending')
                ->count(),

            'preparing' => Order::where('seller_id', $sellerId)
                ->where('status', 'preparing')
                ->count(),

            'ready' => Order::where('seller_id', $sellerId)
                ->where('status', 'ready')
                ->count(),

            'delivered' => Order::where('seller_id', $sellerId)
                ->where('status', 'delivered')
                ->count(),
        ];

        $insights = [];

/*
|--------------------------------------------------------------------------
| Rule 1: Best-selling product
|--------------------------------------------------------------------------
*/
$bestProduct = Product::where('seller_id', $sellerId)
    ->orderByDesc('sales_count')
    ->first();

if ($bestProduct && $bestProduct->sales_count > 0) {
    $insights[] = [
        'type' => 'success',
        'title' => 'Best-selling product',
        'message' => $bestProduct->name . ' is your best-selling product with ' . $bestProduct->sales_count . ' sales.',
    ];
}

/*
|--------------------------------------------------------------------------
| Rule 2: High likes but low sales
|--------------------------------------------------------------------------
*/
$highLikesLowSales = Product::where('seller_id', $sellerId)
    ->where('likes_count', '>=', 10)
    ->whereColumn('likes_count', '>', DB::raw('sales_count * 2'))
    ->orderByDesc('likes_count')
    ->first();

if ($highLikesLowSales) {
    $insights[] = [
        'type' => 'warning',
        'title' => 'High interest, low sales',
        'message' => $highLikesLowSales->name . ' has high likes but low sales. Consider adding an offer or improving the product description.',
    ];
}

/*
|--------------------------------------------------------------------------
| Rule 3: Offer recommendation
|--------------------------------------------------------------------------
*/
$offerCandidate = Product::where('seller_id', $sellerId)
    ->where('likes_count', '>=', 10)
    ->where('stock', '>=', 10)
    ->where('sales_count', '<=', 50)
    ->where('is_sale', false)
    ->orderByDesc('likes_count')
    ->first();

if ($offerCandidate) {
    $insights[] = [
        'type' => 'info',
        'title' => 'Offer recommendation',
        'message' => $offerCandidate->name .
            ' has many likes and enough stock, but its sales are still low. Creating a discount offer may help increase purchases.',
    ];
}

/*
|--------------------------------------------------------------------------
| Rule 4: Restock recommendation
|--------------------------------------------------------------------------
*/
$lowStockProduct = Product::where('seller_id', $sellerId)
    ->where('stock', '<=', 5)
    ->where('sales_count', '>', 0)
    ->orderByDesc('sales_count')
    ->first();

if ($lowStockProduct) {
    $insights[] = [
        'type' => 'alert',
        'title' => 'Restock recommended',
        'message' => $lowStockProduct->name . ' is selling well and has low stock. Restock it soon.',
    ];
}

/*
|--------------------------------------------------------------------------
| Rule 5: Monthly sales trend
|--------------------------------------------------------------------------
*/
$salesCollection = collect($salesOverTime)->values();

if ($salesCollection->count() >= 2) {
    $firstMonth = $salesCollection->first();
    $lastMonth = $salesCollection->last();

    $firstValue = (float) $firstMonth->total;
    $lastValue = (float) $lastMonth->total;

    if ($lastValue > $firstValue) {
        $insights[] = [
            'type' => 'success',
            'title' => 'Monthly sales growth',
            'message' => 'Monthly sales increased from ₪' . number_format($firstValue, 2) .
                ' to ₪' . number_format($lastValue, 2) .
                ' from the first recorded month to the latest recorded month.',
        ];
    } elseif ($lastValue < $firstValue) {
        $insights[] = [
            'type' => 'warning',
            'title' => 'Monthly sales decrease',
            'message' => 'Monthly sales decreased from ₪' . number_format($firstValue, 2) .
                ' to ₪' . number_format($lastValue, 2) .
                ' from the first recorded month to the latest recorded month.',
        ];
    }
}

/*
|--------------------------------------------------------------------------
| Rule 6: Monthly sales recovery
|--------------------------------------------------------------------------
*/
if ($salesCollection->count() >= 3) {
    $lowestIndex = $salesCollection
        ->values()
        ->search(function ($item) use ($salesCollection) {
            return (float) $item->total == (float) $salesCollection->min('total');
        });

    $lastMonth = $salesCollection->values()->last();
    $lowestValue = (float) $salesCollection->min('total');
    $lastValue = (float) $lastMonth->total;

    if ($lowestIndex !== false && $lowestIndex < $salesCollection->count() - 1 && $lastValue > ($lowestValue * 2)) {
        $insights[] = [
            'type' => 'success',
            'title' => 'Monthly sales recovery',
            'message' => 'Monthly sales recovered after a recent decline and reached ₪' .
                number_format($lastValue, 2) . ' in the latest sales month.',
        ];
    }
}

/*
|--------------------------------------------------------------------------
| Rule 7: Pending orders alert
|--------------------------------------------------------------------------
*/
$pendingOrders = Order::where('seller_id', $sellerId)
    ->where('status', 'pending')
    ->count();

if ($pendingOrders > 0) {
    $insights[] = [
        'type' => 'warning',
        'title' => 'Pending orders need attention',
        'message' => 'You have ' . $pendingOrders . ' pending order(s). Review them to avoid delays.',
    ];
}

/*
|--------------------------------------------------------------------------
| Rule 8: Sales performance summary
|--------------------------------------------------------------------------
*/
if ($totalSales > 0) {
    $insights[] = [
        'type' => 'info',
        'title' => 'Sales performance',
        'message' => 'Your delivered sales reached ₪' . number_format($totalSales, 2) .
            '. Keep tracking your best-performing products.',
    ];
}

        return response()->json([
            'success' => true,
            'data' => [
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'total_likes' => $totalLikes,
                'followers' => $followers,
                'sales_over_time' => $salesOverTime,
                'top_products' => $topProducts,
                'orders_status' => $ordersStatus,
                'insights' => $insights,
            ]
        ]);
    }
}

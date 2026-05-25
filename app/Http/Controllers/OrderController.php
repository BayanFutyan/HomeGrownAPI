<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $orders = Order::with(['items.product', 'items.details', 'seller'])
            ->where('customer_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $this->formatOrders($orders),
        ]);
    }

    public function sellerOrders()
    {
        $sellerId = Auth::id();

        $orders = Order::with(['items.product', 'items.details', 'customer'])
            ->where('seller_id', $sellerId)
            ->orderBy('created_at', 'desc')
            ->paginate(4);

        return response()->json([
            'success' => true,
            'data' => $this->formatOrdersForSeller($orders),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show($id)
    {
        $userId = Auth::id();

        $order = Order::with(['items.product', 'items.details', 'customer'])
            ->where(function ($query) use ($userId) {
                $query->where('customer_id', $userId)
                    ->orWhere('seller_id', $userId);
            })
            ->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or access denied',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatOrderDetails($order),
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,shipped,delivered,cancelled',
        ]);

        $sellerId = Auth::id();

        $order = Order::where('seller_id', $sellerId)->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or you are not the seller',
            ], 404);
        }

        $order->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => [
                'id' => $order->id,
                'status' => $order->status,
                'status_label' => $this->getStatusLabel($order->status),
            ],
        ]);
    }

    public function cancel($id)
    {
        $customerId = Auth::id();

        $order = Order::where('customer_id', $customerId)
            ->whereIn('status', ['pending', 'preparing'])
            ->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled or not found',
            ], 404);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
        ]);
    }

    public function ordersSummary()
    {
        $sellerId = Auth::id();

        return response()->json([
            'success' => true,
            'data' => [
                'new' => Order::where('seller_id', $sellerId)->where('status', 'pending')->count(),
                'preparing' => Order::where('seller_id', $sellerId)->where('status', 'preparing')->count(),
                'ready' => Order::where('seller_id', $sellerId)->where('status', 'shipped')->count(),
                'delivered' => Order::where('seller_id', $sellerId)->where('status', 'delivered')->count(),
            ],
        ]);
    }

    public function getAllSellerOrders()
    {
        $sellerId = Auth::id();

        $orders = Order::with(['items.product', 'items.details', 'customer'])
            ->where('seller_id', $sellerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $this->formatOrdersForSeller($orders),
        ]);
    }

private function formatOrders($orders)
{
    return $orders->map(function ($order) {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'order_date' => $order->created_at->format('M d, Y - h:i A'),
            'status' => $order->status,
            'status_label' => $this->getStatusLabel($order->status),
            'total_items' => $order->items->sum('quantity'),
            'total_amount' => $order->total_amount,
            'payment_method' => $order->payment_method,
            'shipping_address' => $order->shipping_address,
            'seller' => [
        'id' => $order->seller->id ?? null,
        'name' => $order->seller->name ?? 'Artisan',
        'profile_image' => $order->seller->profile_image ?? null,
        'address' => $order->seller->address ?? null,
    ],

            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'Unknown',
                    'product_image' => $item->product->image ?? null,
                    'quantity' => $item->quantity,
                    'price' => $item->product_price,
                    'subtotal' => $item->subtotal,
                ];
            })->values(),
        ];
    })->values();
}

    private function formatOrdersForSeller($orders)
    {
        if ($orders instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $orders = $orders->getCollection();
        }

        return $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer->name ?? 'Unknown',
                'customer_avatar' => $order->customer->profile_image ?? null,
                'order_date' => $order->created_at->format('M d, Y - h:i A'),
                'status' => $order->status,
                'status_label' => $this->getStatusLabel($order->status),
                'total_items' => $order->items->sum('quantity'),
                'total_amount' => $order->total_amount,
            ];
        });
    }

    private function formatOrderDetails($order)
    {
        return [
            'id' => $order->id,
            'order_number' => '#'.$order->order_number,
            'date' => $order->created_at->format('M d, Y • h:i A'),
            'payment_method' => $order->payment_method,
            'status' => $order->status,
            'status_label' => $this->getStatusLabel($order->status),
            'customer' => [
                'name' => $order->customer->name ?? 'Unknown',
                'email' => $order->customer->email ?? '',
                'phone' => $order->customer->phone ?? '',
                'avatar' => $order->customer->profile_image ?? null,
            ],
            'delivery' => [
                'address' => $order->shipping_address,
                'method' => 'Local Delivery',
                'time_slot' => $order->delivery_time_slot,
            ],
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'Unknown',
                    'product_category' => $item->product->category ?? '',
                    'product_image' => $item->product->image ?? null,
                    'quantity' => $item->quantity,
                    'price' => $item->product_price,
                    'subtotal' => $item->subtotal,
                    'customizations' => $item->details->map(function ($detail) {
                        return $detail->detail_name . ': ' . $detail->detail_value;
                    })->toArray(),
                ];
            }),
            'summary' => [
                'subtotal' => $order->subtotal,
                'delivery_fee' => $order->delivery_fee,
                'discount' => $order->discount,
                'total' => $order->total_amount,
            ],
            'customer_note' => $order->note,
            'can_update_status' => true,
            'can_cancel' => in_array($order->status, ['pending', 'preparing']),
        ];
    }

    private function getStatusLabel($status)
    {
        return match ($status) {
            'pending' => 'New',
            'preparing' => 'Preparing',
            'shipped' => 'Ready',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst($status),
        };
    }
}
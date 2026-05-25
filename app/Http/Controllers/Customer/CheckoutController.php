<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => 'nullable|string',
            'note' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'delivery_time_slot' => 'nullable|string',
        ]);

        $cart = Cart::where('customer_id', $request->user()->id)
            ->with(['items.product.offer', 'items.product.seller'])
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found',
            ], 404);
        }

        $items = $cart->items->where('is_selected', true);

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No selected items',
            ], 422);
        }

        return DB::transaction(function () use ($items, $request, $cart) {
            $orders = [];

            $groupedBySeller = $items->groupBy(function ($item) {
                return $item->product->seller_id;
            });

            foreach ($groupedBySeller as $sellerId => $sellerItems) {
                $subtotal = 0;

                foreach ($sellerItems as $item) {
                    $product = $item->product;

                    if (!$product) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Product not found',
                        ], 404);
                    }

                    if ($product->stock < $item->quantity) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Not enough stock for ' . $product->name,
                        ], 422);
                    }

                    $price = $product->offer
                        ? $product->offer->discounted_price
                        : $product->price;

                    $subtotal += $price * $item->quantity;
                }

                $deliveryFee = 15;
                $discount = 0;
                $total = $subtotal + $deliveryFee - $discount;

                $order = Order::create([
                    'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . rand(100, 999),
                    'customer_id' => $request->user()->id,
                    'seller_id' => $sellerId,
                    'payment_method' => $request->payment_method ?? 'cash',
                    'status' => 'pending',
                    'subtotal' => $subtotal,
                    'delivery_fee' => $deliveryFee,
                    'discount' => $discount,
                    'total_amount' => $total,
                    'note' => $request->note,
                    'shipping_address' => $request->shipping_address,
                    'delivery_time_slot' => $request->delivery_time_slot,
                ]);

                foreach ($sellerItems as $item) {
                    $product = $item->product;

                    $price = $product->offer
                        ? $product->offer->discounted_price
                        : $product->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item->quantity,
                        'product_price' => $price,
                        'subtotal' => $price * $item->quantity,
                    ]);

                    $product->decrement('stock', $item->quantity);
                    $product->increment('sales_count', $item->quantity);
                }

                $orders[] = $order->load(['items.product', 'seller']);
            }

            CartItem::where('cart_id', $cart->id)
                ->where('is_selected', true)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $orders,
            ], 201);
        });
    }
}
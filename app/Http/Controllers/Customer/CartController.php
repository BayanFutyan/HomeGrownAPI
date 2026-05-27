<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getCart(Request $request)
    {
        return Cart::firstOrCreate([
            'customer_id' => $request->user()->id,
        ]);
    }

    public function index(Request $request)
    {
        $cart = $this->getCart($request)->load([
            'items.product.seller',
            'items.product.offer',
            'items.product.details',
        ]);

        return response()->json([
            'success' => true,
            'data' => $cart,
            'message' => 'Cart retrieved successfully',
        ]);
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $cart = $this->getCart($request);

        $product = Product::whereNull('deleted_at')
            ->findOrFail($request->product_id);

        if ($product->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Product is out of stock',
            ], 422);
        }

        $requestedQuantity = $request->quantity ?? 1;

        if ($requestedQuantity > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Requested quantity is greater than stock',
            ], 422);
        }

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $newQuantity = $item->quantity + $requestedQuantity;

            if ($newQuantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock',
                ], 422);
            }

            $item->update([
                'quantity' => $newQuantity,
                'is_selected' => true,
            ]);
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $requestedQuantity,
                'is_selected' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'data' => $item->load([
                'product.seller',
                'product.offer',
                'product.details',
            ]),
        ], 201);
    }

    public function updateQuantity(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::whereHas('cart', function ($q) use ($request) {
            $q->where('customer_id', $request->user()->id);
        })->with('product')->findOrFail($id);

        if ($request->quantity > $item->product->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock',
            ], 422);
        }

        $item->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quantity updated successfully',
            'data' => $item->load([
                'product.seller',
                'product.offer',
                'product.details',
            ]),
        ]);
    }

    public function updateSelection(Request $request, $id)
    {
        $request->validate([
            'is_selected' => 'required|boolean',
        ]);

        $item = CartItem::whereHas('cart', function ($q) use ($request) {
            $q->where('customer_id', $request->user()->id);
        })->findOrFail($id);

        $item->update([
            'is_selected' => $request->is_selected,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Selection updated successfully',
            'data' => $item,
        ]);
    }

    public function selectAll(Request $request)
    {
        $request->validate([
            'is_selected' => 'required|boolean',
        ]);

        $cart = $this->getCart($request);

        CartItem::where('cart_id', $cart->id)->update([
            'is_selected' => $request->is_selected,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart selection updated successfully',
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $item = CartItem::whereHas('cart', function ($q) use ($request) {
            $q->where('customer_id', $request->user()->id);
        })->findOrFail($id);

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
        ]);
    }
}

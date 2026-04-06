<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the products (for the authenticated artisan)
     */
    public function index(Request $request)
    {
        ///** @var \App\Models\User|null $user */
        // $user = Auth::user();
         $user = \App\Models\User::find(1); 
        
        // Temporary: use user id 1 if no authenticated user
        if (!$user) {
            $products = Product::with('offer')->paginate(4);
            return response()->json([
                'data' => $products,
                'message' => 'Products retrieved successfully'
            ]);
        }
        
        if (!$user->isArtisan()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $query = Product::where('seller_id', $user->id);
        
        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Status filter
        if ($request->has('status')) {
            if ($request->status == 'Active') {
                $query->where('stock', '>', 0);
            } elseif ($request->status == 'Out of Stock') {
                $query->where('stock', 0);
            }
        }
        
        // Sort filter
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'Oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'Price High':
                    $query->orderBy('price', 'desc');
                    break;
                case 'Price Low':
                    $query->orderBy('price', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->with('offer')->paginate(10);
        
        return response()->json([
            'data' => $products,
            'message' => 'Products retrieved successfully'
        ]);
    }
    
    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        ///** @var \App\Models\User|null $user */
        //$user = Auth::user();
         $user = \App\Models\User::find(1); 
        // Check if user exists
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }
        
        if (!$user->isArtisan()) {
            return response()->json(['message' => 'Only artisans can add products'], 403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string',
        ]);
        
        $product = Product::create([
            'seller_id' => $user->id,
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $request->image,
            'likes_count' => 0,
            'is_sale' => false,
            'sales_count' => 0,
        ]);
        
        return response()->json([
            'data' => $product,
            'message' => 'Product created successfully'
        ], 201);
    }
    
    /**
     * Display the specified product
     */
    public function show($id)
    {
        $product = Product::with(['offer', 'comments.user', 'details'])->find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        return response()->json([
            'data' => $product,
            'message' => 'Product retrieved successfully'
        ]);
    }
    
    /**
     * Update the specified product
     */
    public function update(Request $request, $id)
    {
        //$user = Auth::user();
        $user = \App\Models\User::find(1);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }
        
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        if ($product->seller_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'image' => 'nullable|string',
        ]);
        
        $product->update($request->only(['name', 'category', 'description', 'price', 'stock', 'image']));
        
        return response()->json([
            'data' => $product,
            'message' => 'Product updated successfully'
        ]);
    }
    
    /**
     * Update product description only
     */
    public function updateDescription(Request $request, $id)
    {
        //$user = Auth::user();
        $user = \App\Models\User::find(1);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }
        
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        if ($product->seller_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'description' => 'required|string',
        ]);
        
        $product->update(['description' => $request->description]);
        
        return response()->json([
            'data' => $product,
            'message' => 'Description updated successfully'
        ]);
    }
    
    /**
     * Remove the specified product
     */
    public function destroy($id)
    {
        //$user = Auth::user();
        $user = \App\Models\User::find(1);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }
        
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        if ($product->seller_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $product->delete();
        
        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
    
    /**
     * Add or update offer for a product
     */
    public function addOffer(Request $request, $id)
    {
        //$user = Auth::user();
        $user = \App\Models\User::find(1);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }
        
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        if ($product->seller_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'discount_value' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        $discountedPrice = $product->price - ($product->price * $request->discount_value / 100);
        
        $offer = Offer::updateOrCreate(
            ['product_id' => $id],
            [
                'discount_value' => $request->discount_value,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'discounted_price' => $discountedPrice,
            ]
        );
        
        $product->update(['is_sale' => true]);
        
        return response()->json([
            'data' => $product->load('offer'),
            'message' => 'Offer added successfully'
        ]);
    }
    
    /**
     * Remove offer from a product
     */
    public function removeOffer($id)
    {
        //$user = Auth::user();
        $user = \App\Models\User::find(1);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }
        
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        if ($product->seller_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        if ($product->offer) {
            $product->offer->delete();
        }
        
        $product->update(['is_sale' => false]);
        
        return response()->json([
            'data' => $product,
            'message' => 'Offer removed successfully'
        ]);
    }
    
    /**
     * Get product details (product_details table)
     */
    public function getDetails($id)
    {
        $product = Product::with('details')->find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        return response()->json([
            'data' => [
                'product' => $product,
                'details' => $product->details,
            ],
            'message' => 'Product details retrieved successfully'
        ]);
    }
    
    /**
     * Add detail to product
     */
    public function addDetail(Request $request, $id)
    {
        //$user = Auth::user();
        $user = \App\Models\User::find(1);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }
        
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        if ($product->seller_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'detail_name' => 'required|string|max:255',
            'detail_value' => 'required|string|max:255',
        ]);
        
        $detail = $product->details()->create([
            'detail_name' => $request->detail_name,
            'detail_value' => $request->detail_value,
        ]);
        
        return response()->json([
            'data' => $detail,
            'message' => 'Detail added successfully'
        ], 201);
    }
    
    /**
     * Update all product details
     */
    public function updateAllDetails(Request $request, $id)
    {
       // $user = Auth::user();
        $user = \App\Models\User::find(1);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }
        
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        if ($product->seller_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'details' => 'required|array',
            'details.*.detail_name' => 'required|string',
            'details.*.detail_value' => 'required|string',
        ]);
        
        // Delete old details
        $product->details()->delete();
        
        // Add new details
        foreach ($request->details as $detail) {
            $product->details()->create($detail);
        }
        
        return response()->json([
            'data' => $product->details,
            'message' => 'All details updated successfully'
        ]);
    }
    
    /**
     * Get comments for a product
     */
   public function getComments($id)
{
    $product = Product::find($id);
    
    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }
    
    // جلب جميع التعليقات مع المستخدم
    $comments = $product->comments()->with('user')->get();
    
    return response()->json([
        'data' => $comments,
        'message' => 'Comments retrieved successfully'
    ]);
}
}
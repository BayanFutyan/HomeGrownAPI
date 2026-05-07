<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Get current user (temporary for testing)
     */
    private function getCurrentUser()
    {
        /** @var User|null $user */
         $user = Auth::user();
        
        
        return $user;
    }

    /**
     * Upload image to storage
     */
    private function uploadImage($imageFile)
    {
        if (!$imageFile) return null;
        
        try {
            $fileName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
            $path = $imageFile->storeAs('products', $fileName, 'public');
            
            return 'storage/' . $path;
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Upload Base64 image from Flutter Web/Mobile
     */
    private function uploadBase64Image($base64String)
    {
        try {
            // إزالة البيانات الوصفية من Base64 (data:image/jpeg;base64,)
            $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $base64String);
            $imageData = str_replace(' ', '+', $imageData);
            $decodedImage = base64_decode($imageData);
            
            if ($decodedImage === false) {
                Log::error('Base64 decode failed');
                return null;
            }
            
            // تحديد نوع الصورة من الـ Base64
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $decodedImage);
            finfo_close($finfo);
            
            $extension = match($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                default => 'jpg'
            };
            
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            $path = 'products/' . $fileName;
            
            Storage::disk('public')->put($path, $decodedImage);
            
            Log::info('Base64 image saved: ' . $path);
            
            return 'storage/' . $path;
        } catch (\Exception $e) {
            Log::error('Base64 image upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Display a listing of the products
     */
    public function index(Request $request)
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            $products = Product::with(['offer' => function($q) {
                $q->where('end_date', '>=', now());
            }])->paginate(4);
            return response()->json([
                'data' => $products,
                'message' => 'Products retrieved successfully'
            ]);
        }
        
        if (!$user->isArtisan()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $query = Product::where('seller_id', $user->id);
        
        // جلب العروض النشطة فقط
        $query->with(['offer' => function($q) {
            $q->where('end_date', '>=', now());
        }]);
        
        // 1. فلترة البحث (Search)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }
        
        // 2. فلترة الحالة (Status Filter)
        if ($request->has('status') && $request->status != 'All Status') {
            switch ($request->status) {
                case 'On Sale':
                    $query->where('is_sale', true);
                    break;
                case 'In Stock':
                    $query->where('stock', '>', 0);
                    break;
                case 'Out of Stock':
                    $query->where('stock', 0);
                    break;
                case 'No Comments':
                    $query->whereDoesntHave('comments');
                    break;
            }
        }
        
        // 3. ترتيب المنتجات (Sort Filter)
        if ($request->has('sort') && $request->sort != 'Newest') {
            switch ($request->sort) {
                case 'Top Selling':
                    $query->orderBy('sales_count', 'desc');
                    break;
                case 'Highest Price':
                    $query->orderBy('price', 'desc');
                    break;
                case 'Lowest Price':
                    $query->orderBy('price', 'asc');
                    break;
                case 'Top Liked':
                    $query->orderBy('likes_count', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(4);
        
        return response()->json([
            'data' => $products,
            'message' => 'Products retrieved successfully'
        ]);
    }
    
    /**
     * Store a newly created product (يدعم الصورة و Base64)
     */
    public function store(Request $request)
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_base64' => 'nullable|string',
        ]);
        
        $imagePath = null;
        
        // 1. رفع صورة عادية (من Postman أو Mobile Multipart)
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));
        }
        // 2. رفع صورة Base64 (من Flutter Web/Mobile)
        else if ($request->image_base64) {
            $imagePath = $this->uploadBase64Image($request->image_base64);
        }
        // 3. رابط صورة خارجي
        else if ($request->image_url) {
            $imagePath = $request->image_url;
        }
        
        $product = Product::create([
            'seller_id' => $user->id,
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
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
        $product = Product::with([
            'offer' => function($q) {
                $q->where('end_date', '>=', now());
            },
            'comments.user',
            'details'
        ])->find($id);
        
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
        Log::info('Update product request', ['id' => $id, 'data' => $request->all()]);
        
        $user = $this->getCurrentUser();
        $product = Product::find($id);
        
        if (!$product || $product->seller_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'image' => 'nullable|string',
            'image_base64' => 'nullable|string',
        ]);
        
        $data = $request->only(['name', 'category', 'description', 'price', 'stock']);
        
        // معالجة الصورة
        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists(str_replace('storage/', '', $product->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $product->image));
            }
            $data['image'] = $this->uploadImage($request->file('image'));
        } else if ($request->image_base64) {
            if ($product->image && Storage::disk('public')->exists(str_replace('storage/', '', $product->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $product->image));
            }
            $data['image'] = $this->uploadBase64Image($request->image_base64);
        } else if ($request->image_url) {
            $data['image'] = $request->image_url;
        }
        
        $product->update($data);
        
        Log::info('Product updated', ['product' => $product]);
        
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
        $user = $this->getCurrentUser();
        
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
        $user = $this->getCurrentUser();
        
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
        $user = $this->getCurrentUser();
        
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
            'data' => $product->load(['offer' => function($q) {
                $q->where('end_date', '>=', now());
            }]),
            'message' => 'Offer added successfully'
        ]);
    }
    
    /**
     * Remove offer from a product
     */
    public function removeOffer($id)
    {
        $user = $this->getCurrentUser();
        
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
        $user = $this->getCurrentUser();
        
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
        $user = $this->getCurrentUser();
        
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
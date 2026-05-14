<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * عرض قائمة المنتجات مع دعم الفلاتر والترتيب
     */
    public function index(Request $request)
    {
        $query = Product::with(['seller', 'offers'])
            ->whereNull('deleted_at');
        
        // ✅ فلتر: المنتجات التي عليها تخفيض (is_sale)
        if ($request->has('is_sale') && filter_var($request->is_sale, FILTER_VALIDATE_BOOLEAN)) {
            $query->where('is_sale', true);
        }
        
        // ✅ فلتر: حسب القسم (category)
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        // ✅ فلتر: بحث (search)
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // ✅ فلتر: أقل سعر (min_price)
        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        
        // ✅ فلتر: أعلى سعر (max_price)
        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // ✅ الترتيب (sort_by و order)
        $sortBy = $request->get('sort_by', 'created_at');
        $order = $request->get('order', 'desc');
        
        // التحقق من صحة أسماء الأعمدة (أمان)
        $allowedSorts = ['created_at', 'likes_count', 'price', 'sales_count'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $order);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate($request->per_page ?? 15);
        
        return ProductResource::collection($products);
    }
    
    /**
     * عرض منتج محدد
     */
    public function show($id)
    {
        $product = Product::with(['seller', 'details', 'offers', 'comments.user'])
            ->whereNull('deleted_at')
            ->findOrFail($id);
        
        return new ProductResource($product);
    }
    
    /**
     * عرض تفاصيل المنتج الإضافية
     */
    public function details($id)
    {
        $product = Product::with('details')->findOrFail($id);
        return response()->json($product->details);
    }
}
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

        // ✅ دعم معامل filter من الـ Frontend
        if ($request->has('filter')) {
            $filter = $request->input('filter');
            
            switch ($filter) {
                case 'New':
                    $query->orderBy('created_at', 'desc');
                    break;
                    
                case 'Most Liked':
                    // ✅ ترتيب حسب عدد الإعجابات (من الأكبر إلى الأصغر)
                    $query->orderBy('likes_count', 'desc');
                    break;
                    
                case 'Offer':
                    $query->where('is_sale', true)->orderBy('created_at', 'desc');
                    break;
                    
                case 'Near You':
                    // للموقع - حالياً نرتب حسب الأحدث
                    $query->orderBy('created_at', 'desc');
                    break;
                    
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            // ✅ الترتيب الافتراضي (sort_by و order)
            $sortBy = $request->get('sort_by', 'created_at');
            $order = $request->get('order', 'desc');

            $allowedSorts = ['created_at', 'likes_count', 'price', 'sales_count'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $order);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        }

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
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('category', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // ✅ فلتر: أقل سعر (min_price)
        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }

        // ✅ فلتر: أعلى سعر (max_price)
        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
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
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Support\Facades\Cache;
use Exception;

class HomeController extends Controller
{
    /*
    |==========================================
    |> Get home page data for the user
    |==========================================
    */
    public function index(Request $request)
    {
        try {
            $data = [
                'categories' => $this->getImportantCategories(),
                // 'promoted_products' => $this->getPromotedProducts(),
                'followed_vendors' => $this->getFollowedVendorsLatestProducts($request),
                // 'demanded_products' => $this->getMostDemandedProducts(),
            ];

            if (empty(array_filter($data))) {
                return response()->json([
                    'status' => false,
                    'message' => 'No data available',
                ], Response::HTTP_OK);
            }

            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


    /*
    |==========================================
    |> Get important categories with 4 products each
    |==========================================
    */
    private function getImportantCategories()
    {
        $cacheKey = 'important_categories_' . md5(json_encode(request()->all()));

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $categories = Category::has('products')->with(['products' => function ($query) {
            $query->take(4)->orderByDesc('created_at');
        }])->limit(5)->get();

        if ($categories->isEmpty()) {
            return [];
        }

        $categories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->category_name,
                'image' => $category->getImageUrl(),
                'products' => $category->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->product_name,
                        'price' => optional($product->details)->min('price') ?? 0.00,
                        'average_rating' => optional($product->reviews())->avg('rating') ?? 0,
                    ];
                }),
            ];
        });

        Cache::put($cacheKey, $categories, now()->addMinutes(10));

        return $categories;
    }

    /*
    |==========================================
    |> Get promoted products
    |==========================================
    */
    private function getPromotedProducts()
    {
        $cacheKey = 'promoted_products_' . md5(json_encode(request()->all()));

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Get approved promotions with valid end dates
        $promotionProductIds = Promotion::where('status', 'approved')
            ->whereDate('end_date', '>=', now())
            ->pluck('product_id');

        if ($promotionProductIds->isEmpty()) {
            return [];
        }

        // Fetch products and their details/vendor relationships
        $products = Product::whereIn('id', $promotionProductIds)
            ->with('details', 'vendor')
            ->take(10)
            ->get();

        if ($products->isEmpty()) {
            return [];
        }

        $products = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->product_name,
                'price' => optional($product->details)->min('price') ?? 0.00,
                'average_rating' => optional($product->reviews())->avg('rating') ?? 0,
                'vendor' => $product->vendor ? [
                    'id' => $product->vendor->id,
                    'name' => $product->vendor->brand_name,
                ] : null,
            ];
        });

        Cache::put($cacheKey, $products, now()->addMinutes(10));

        return $products;
    }
    /*
    |==========================================
    |> Get latest products from followed vendors
    |==========================================
    */
    private function getFollowedVendorsLatestProducts(Request $request)
    {

        $user = auth()->user();

        if (!$user) {
            return [];
        }

        $cacheKey = 'followed_vendors_latest_products_' . $user->id;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $vendors = $user->followedVendors()->pluck('id');

        if ($vendors->isEmpty()) {
            return [];
        }

        $products = Product::whereIn('vendor_id', $vendors)
            ->with('details', 'vendor')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        if ($products->isEmpty()) {
            return [];
        }

        $products = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->product_name,
                'price' => optional($product->details)->min('price') ?? 0.00,
                'average_rating' => optional($product->reviews())->avg('rating') ?? 0,
                'vendor' => $product->vendor ? [
                    'id' => $product->vendor->id,
                    'name' => $product->vendor->brand_name,
                ] : null,
            ];
        });

        Cache::put($cacheKey, $products, now()->addMinutes(10));

        return $products;
    }

    /*
    |==========================================
    |> Get most demanded products
    |==========================================
    */
    private function getMostDemandedProducts()
    {
        $cacheKey = 'most_demanded_products_' . md5(json_encode(request()->all()));

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Get product IDs from approved promotions with valid end dates
        $promotionProductIds = Promotion::where('status', 'approved')
            ->whereDate('end_date', '>=', now())
            ->pluck('product_id');

        if ($promotionProductIds->isEmpty()) {
            return [];
        }

        // Fetch products based on promotions or order quantity
        $products = Product::whereIn('id', $promotionProductIds)
            ->orWhereHas('orders', function ($q) {
                $q->orderByDesc('quantity');
            })
            ->with('details', 'vendor')
            ->take(10)
            ->get();

        if ($products->isEmpty()) {
            return [];
        }

        $products = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->product_name,
                'price' => optional($product->details)->min('price') ?? 0.00,
                'average_rating' => optional($product->reviews())->avg('rating') ?? 0,
                'vendor' => $product->vendor ? [
                    'id' => $product->vendor->id,
                    'name' => $product->vendor->brand_name,
                ] : null,
            ];
        });

        Cache::put($cacheKey, $products, now()->addMinutes(10));

        return $products;
    }
}

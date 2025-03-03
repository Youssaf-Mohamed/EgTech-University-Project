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
                'promoted_products' => $this->getPromotedProducts(),
                'followed_vendors' => $this->getFollowedVendorsLatestProducts($request),
                'demanded_products' => $this->getMostDemandedProducts(),
            ];

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

        $categories = Category::with(['products' => function ($query) {
            $query->take(4)->orderByDesc('created_at');
        }])
            ->limit(5)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->category_name,
                    'image' => $category->getImageUrl(),
                    'products' => $category->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->product_name,
                            'price' => $product->details->min('price') ?? 0.00,
                            'average_rating' => $product->reviews()->avg('rating') ?? 0,
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

        $products = Product::whereIn('id', Promotion::where('status', 'approved')
            ->whereDate('end_date', '>=', now())
            ->pluck('product_id'))
            ->with('details', 'vendor')
            ->take(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'price' => $product->details->min('price') ?? 0.00,
                    'average_rating' => $product->reviews()->avg('rating') ?? 0,
                    'vendor' => [
                        'id' => $product->vendor->id,
                        'name' => $product->vendor->brand_name,
                    ],
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
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'price' => $product->details->min('price') ?? 0.00,
                    'average_rating' => $product->reviews()->avg('rating') ?? 0,
                    'vendor' => [
                        'id' => $product->vendor->id,
                        'name' => $product->vendor->brand_name,
                    ],
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

        $products = Product::whereIn('id', Promotion::where('status', 'approved')
            ->whereDate('end_date', '>=', now())
            ->pluck('product_id'))
            ->orWhereHas('orders', function ($q) {
                $q->orderByDesc('quantity');
            })
            ->with('details', 'vendor')
            ->take(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'price' => $product->details->min('price') ?? 0.00,
                    'average_rating' => $product->reviews()->avg('rating') ?? 0,
                    'vendor' => [
                        'id' => $product->vendor->id,
                        'name' => $product->vendor->brand_name,
                    ],
                ];
            });

        Cache::put($cacheKey, $products, now()->addMinutes(10));

        return $products;
    }
}

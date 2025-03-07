<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Exception;

class HomeController extends Controller
{
    /*
    |==========================================
    |> Get most demanded products
    |==========================================
    */
    public function getMostDemandedProducts()
    {
        try {
            return Cache::remember('most_demanded_products', now()->addMinutes(10), function () {
                return Product::with(['details' => function ($query) {
                    $query->select('id', 'product_id', 'price', 'discount');
                }, 'vendor:id,brand_name'])
                    ->whereHas('vendor.promotions', function ($query) {
                        $query->where('promotions.status', 'active');
                    })
                    ->orderByDesc(function ($query) {
                        $query->selectRaw('COALESCE(AVG(reviews.rating), 0)')
                            ->from('reviews')
                            ->whereColumn('reviews.product_id', 'products.id');
                    })
                    ->limit(10)
                    ->get()
                    ->map(function ($product) {
                        return [
                            'product_id' => $product->id,
                            'product_name' => $product->product_name,
                            'description' => $product->description,
                            'product_image' => $product->details->first()?->getImageUrl() ?? asset('images/product-placeholder.jpg'),
                            'vendor_image' => $product->vendor?->getImageUrl() ?? asset('images/vendor-placeholder.jpg'),
                            'details_id' => $product->details->first()?->id,
                            'price' => $product->details->first()?->price,
                            'discount' => $product->details->first()?->discount,
                            'brand_name' => $product->vendor?->brand_name,
                        ];
                    });
            });
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error: ' . $e->getMessage()],
                500
            );
        }
    }

    /*
    |==========================================
    |> Get important categories with products
    |==========================================
    */
    public function getImportantCategories()
    {
        try {
            return Cache::remember('important_categories', now()->addMinutes(10), function () {
                return Category::with(['products' => function ($query) {
                    $query->orderByDesc('created_at')->limit(4)
                        ->with(['details' => function ($q) {
                            $q->select('id', 'product_id', 'price', 'discount');
                        }]);
                }])
                    ->has('products')
                    ->limit(5)
                    ->get()
                    ->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'category_name' => $category->category_name,
                            'description' => $category->description,
                            'category_image' => $category->getImageUrl(),
                            'products' => $category->products->map(function ($product) {
                                return [
                                    'product_id' => $product->id,
                                    'product_name' => $product->product_name,
                                    'product_image' => $product->details->first()?->getImageUrl() ?? asset('images/product-placeholder.jpg'),
                                    'price' => $product->details->first()?->price,
                                    'discount' => $product->details->first()?->discount,
                                ];
                            }),
                        ];
                    });
            });
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error: ' . $e->getMessage()],
                500
            );
        }
    }
    /*
    |==========================================
    |> Get promoted products
    |==========================================
    */
    public function getPromotedProducts()
    {
        try {
            return Cache::remember('promoted_products', now()->addMinutes(10), function () {
                return Product::with(['details' => function ($query) {
                    $query->select('id', 'product_id', 'price', 'discount');
                }, 'vendor:id,brand_name'])
                    ->whereHas('vendor.promotions', function ($query) {
                        $query->where('vendor_promotion.status', 'approved')
                            ->whereDate('vendor_promotion.start_date', '<=', now())
                            ->whereDate('vendor_promotion.end_date', '>=', now());
                    })
                    ->get()
                    ->map(function ($product) {
                        return [
                            'product_id' => $product->id,
                            'product_name' => $product->product_name,
                            'product_image' => $product->details->first()?->getImageUrl() ?? asset('images/product-placeholder.jpg'),
                            'description' => $product->description,
                            'details_id' => $product->details->first()?->id,
                            'price' => $product->details->first()?->price,
                            'discount' => $product->details->first()?->discount,
                            'brand_name' => $product->vendor?->brand_name,
                        ];
                    });
            });
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error: ' . $e->getMessage()],
                500
            );
        }
    }

    /*
    |==========================================
    |> Get latest products from followed vendors
    |==========================================
    */
    public function getFollowedVendorsLatestProducts(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(
                    ['status' => false, 'message' => 'Unauthorized'],
                    403
                );
            }

            $userId = Auth::id();

            return Cache::remember("followed_vendors_latest_products_{$userId}", now()->addMinutes(10), function () use ($userId) {
                return Product::with(['details' => function ($query) {
                    $query->select('id', 'product_id', 'price', 'discount');
                }, 'vendor:id,brand_name'])
                    ->whereIn('vendor_id', function ($query) use ($userId) {
                        $query->select('vendor_id')
                            ->from('follows')
                            ->where('user_id', $userId);
                    })
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get()
                    ->map(function ($product) {
                        return [
                            'product_id' => $product->id,
                            'product_name' => $product->product_name,
                            'product_image' => $product->details->first()?->getImageUrl() ?? asset('images/product-placeholder.jpg'),
                            'price' => $product->details->first()?->price,
                            'discount' => $product->details->first()?->discount,
                            'brand_name' => $product->vendor?->brand_name,
                        ];
                    });
            });
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error: ' . $e->getMessage()],
                500
            );
        }
    }
    /*
    |==========================================
    |> Index: Combine all data for the main page
    |==========================================
    */
    public function index()
    {
        try {
            $data = [
                'most_demanded' => $this->getMostDemandedProducts(),
                'categories' => $this->getImportantCategories(),
                'promoted_products' => $this->getPromotedProducts(),
                'followed_products' => Auth::check() ? $this->getFollowedVendorsLatestProducts(request()) : [],
            ];

            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error: ' . $e->getMessage()],
                500
            );
        }
    }
}

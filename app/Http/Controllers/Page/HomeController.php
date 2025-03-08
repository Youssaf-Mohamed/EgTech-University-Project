<?php

namespace App\Http\Controllers\Page;

use App\Models\Product;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{

    public function index()
    {
        try {
            $data = [
                'most_demanded' => $this->getMostDemandedProducts(),
                'ads' => $this->getAds(),
                'categories' => $this->getCategories(),
                'promoted_products' => $this->getPromotedProducts(),
                'trending_categories' => $this->getTrendingCategories()
            ];

            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function getMostDemandedProducts()
    {
        try {
            return Product::with(['details' => function ($query) {
                $query->select('id', 'product_id', 'price', 'discount');
            }, 'vendor:id,brand_name'])
                ->whereHas('reviews')
                ->orderByDesc(function ($query) {
                    return $query->selectRaw('COALESCE(AVG(reviews.rating), 0)')
                        ->from('reviews')
                        ->whereColumn('reviews.product_id', 'products.id');
                })
                ->take(12)
                ->get()
                ->map(function ($product) {
                    $detail = $product->details->first();
                    return [
                        'product_id' => $product->id,
                        'product_name' => $product->product_name,
                        'product_image' => $detail?->getImageUrl() ?? asset('images/product-placeholder.jpg'),
                        'vendor_image' => $product->vendor?->getImageUrl() ?? asset('images/vendor-placeholder.jpg'),
                        'price' => $detail?->price,
                        'discount' => $detail?->discount,
                        'brand_name' => $product->vendor?->brand_name,
                        'average_rating' => $product->getAverageRatingAttribute(),
                    ];
                });
        } catch (\Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error: ' . $e->getMessage()],
                500
            );
        }
    }

    private function getAds()
    {
        return Advertisement::where('target_screen', 'home')
            ->take(7)
            ->get()
            ->map(function ($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'created_at' => $ad->created_at,
                    'bg_image' => $ad->getImageUrl() ?? asset('images/advertisement-placeholder.jpg'),
                    'secondry_text' => $ad->brief,
                    'brief' => $ad->brief,
                    'target_screen' => $ad->target_screen,
                    'redirect_to' => $ad->redirect_to,
                ];
            });
    }

    private function getCategories()
    {
        return Category::take(7)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'category_name' => $category->category_name,
                    'description' => $category->description,
                    'category_image' => $category->getImageUrl() ?? asset('images/category-placeholder.jpg'),
                ];
            });
    }

    private function getPromotedProducts()
    {
        $currentDate = Carbon::now();
        return Product::join('vendors', 'products.vendor_id', '=', 'vendors.id')
            ->join('vendor_promotion', 'vendors.id', '=', 'vendor_promotion.vendor_id')
            ->join('promotions', 'vendor_promotion.promotion_id', '=', 'promotions.id')
            ->where('vendor_promotion.status', operator: 'approved')
            ->where('vendor_promotion.start_date', '<=', $currentDate)
            ->where('vendor_promotion.end_date', '>=', $currentDate)
            ->select('products.*', 'vendors.brand_name', 'vendors.id as vendor_id')
            ->take(7)
            ->get()
            ->map(function ($product) {
                $detail = $product->details->first();
                return [
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
                    'product_image' => $detail?->getImageUrl() ?? asset('images/product-placeholder.jpg'),
                    'vendor_image' => $product->vendor?->getImageUrl() ?? asset('images/vendor-placeholder.jpg'),
                    'brand_name' => $product->vendor?->brand_name,
                    'price' => $detail?->price,
                    'discount' => $detail?->discount,
            ];
            });
    }

    private function getTrendingCategories()
    {
        return Category::with(['products' => function ($query) {
            $query->take(7);
        }])
            ->take(4)
            ->get()
            ->map(function ($category) {
                return [
                    'category_id' => $category->id,
                    'category_name' => $category->category_name,
                    'products' => $category->products->map(function ($product) {
                        $detail = $product->details->first();
                        return [
                            'product_id' => $product->id,
                            'product_name' => $product->product_name,
                            'product_image' => $detail?->getImageUrl() ?? asset('images/product-placeholder.jpg'),
                            'price' => $detail?->price,
                            'discount' => $detail?->discount,
                        ];
                    }),
                ];
            });
    }

    public function getFollowedProducts()
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $user = Auth::user();
            $followedVendors = $user->followedVendors;

            $products = Product::whereIn('vendor_id', $followedVendors->pluck('id'))
                ->with(['details' => function ($query) {
                    $query->select('id', 'product_id', 'price', 'discount');
                }, 'vendor:id,brand_name'])
                ->take(7)
                ->get()
                ->map(function ($product) {
                    $detail = $product->details->first();
                    return [
                        'product_id' => $product->id,
                        'product_name' => $product->product_name,
                        'product_image' => $detail?->getImageUrl() ?? asset('images/product-placeholder.jpg'),
                        'vendor_image' => $product->vendor?->getImageUrl() ?? asset('images/vendor-placeholder.jpg'),
                        'brand_name' => $product->vendor?->brand_name,
                        'price' => $detail?->price,
                        'discount' => $detail?->discount,
                    ];
                });

            return response()->json([
                'status' => true,
                'data' => $products,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}

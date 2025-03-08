<?php

namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FollowingStoresController extends Controller
{
    public function index()
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
                ->with(['details', 'vendor'])
                ->inRandomOrder()
                ->orderByDesc('created_at')
                ->take(20)
                ->get();

            $response = [
                'status' => true,
                'data' => [
                    'nav' => [
                        'title' => 'Discover the best products from your favorite stores',
                        'image' => asset('images/navigation-placeholder.png'),
                    ],
                    'vendors' => $followedVendors->map(function ($vendor) {
                        return [
                            'vendor_id' => $vendor->id,
                            'brand_name' => $vendor->brand_name,
                            'vendor_image' => $vendor->getImageUrl(),
                        ];
                    }),
                    'products' => $products->map(function ($product) {
                        $detail = $product->details->first();
                        return [
                            'id' => $product->id,
                            'product_name' => $product->product_name,
                            'price' => $detail?->price ?? 0,
                            'discount' => $detail?->discount ?? 0,
                            'vendor_name' => optional($product->vendor)->brand_name ?? 'N/A',
                            'vendor_image' => optional($product->vendor)->getImageUrl() ?? asset('images/vendor-placeholder.jpg'),
                            'product_image' => $detail?->getImageUrl() ?? asset('images/product-placeholder.jpg'),
                            'rating' => Cache::remember('product-rating-' . $product->id, now()->addMinutes(10), function () use ($product) {
                                return $product->reviews->avg('rating') ?? 0;
                            }),
                            // 'region_name' => $product->vendor?->regions->pluck('name')->first() ?? 'N/A',
                        ];
                    }),
                ],
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}

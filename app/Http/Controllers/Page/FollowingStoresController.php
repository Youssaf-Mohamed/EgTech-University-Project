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
                        return [
                            'id' => $product->id,
                            'category_name' => optional($product->categories)->first()?->category_name ?? 'N/A',
                            'description' => $product->description,
                            'category_image' => optional($product->categories)->first()?->getImageUrl() ?? asset('images/category-placeholder.jpg'),
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

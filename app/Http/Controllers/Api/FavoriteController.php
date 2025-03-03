<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Access\AuthorizationException;
use Exception;
use Illuminate\Http\Response;

class FavoriteController extends Controller
{
    /*
    |==========================================
    |> Add a product to user's favorites
    |==========================================
    */
    public function add(Request $request, Product $product)
    {
        try {
            $user = auth()->user();

            if ($user->favoriteProducts()->toggle($product)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Product added to favorites successfully',
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Product already in favorites',
            ], Response::HTTP_BAD_REQUEST);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /*
    |==========================================
    |> Remove a product from user's favorites
    |==========================================
    */
    public function remove(Request $request, Product $product)
    {
        try {
            $user = auth()->user();

            if ($user->favoriteProducts()->detach($product)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Product removed from favorites successfully',
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Product not found in favorites',
            ], Response::HTTP_BAD_REQUEST);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /*
    |==========================================
    |> Get all favorite products of the authenticated user
    |==========================================
    */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            $query = $user->favoriteProducts();

            if ($request->has('search')) {
                $query->where('product_name', 'LIKE', "%{$request->search}%");
            }

            if ($request->has('price_range')) {
                [$minPrice, $maxPrice] = explode(',', $request->price_range);
                $query->whereHas('details', function ($q) use ($minPrice, $maxPrice) {
                    $q->whereBetween('price', [(float)$minPrice, (float)$maxPrice]);
                });
            }

            if ($request->has('sort_by')) {
                switch ($request->sort_by) {
                    case 'latest':
                        $query->orderByDesc('created_at');
                        break;
                    case 'lowest_price':
                        $query->whereHas('details', function ($q) {
                            $q->orderBy('price');
                        })->withMax('details', 'price');
                        break;
                    case 'highest_price':
                        $query->whereHas('details', function ($q) {
                            $q->orderByDesc('price');
                        })->withMax('details', 'price');
                        break;
                }
            }

            $cacheKey = 'user_favorites_' . $user->id . '_' . md5(json_encode($request->all()));

            if (Cache::has($cacheKey)) {
                $products = Cache::get($cacheKey);
            } else {
                $products = $query->paginate();
                Cache::put($cacheKey, $products, now()->addMinutes(5));
            }

            return response()->json([
                'status' => true,
                'data' => \App\Http\Resources\ProductResource::collection($products),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                ],
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /*
    |==========================================
    |> Check if a product is in user's favorites
    |==========================================
    */
    public function check(Request $request, Product $product)
    {
        try {
            $user = auth()->user();

            $isFavorited = $user->favoriteProducts()->where('product_id', $product->id)->exists();

            return response()->json([
                'status' => true,
                'data' => [
                    'is_favorited' => $isFavorited,
                ],
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}

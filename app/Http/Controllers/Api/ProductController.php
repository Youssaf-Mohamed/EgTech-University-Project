<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\Promotion;
use App\Http\Resources\ProductResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Cache;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\ProductStoreRequest;


class ProductController extends Controller
{
    use AuthorizesRequests;


    /*
    |==========================================
    |> Get all products with filtering and sorting
    |==========================================
    */
    public function index(Request $request)
    {
        try {
            $query = Product::with(['details', 'vendor', 'categories']);

            if ($request->has('vendor_id')) {
                $vendor = Vendor::findOrFail($request->vendor_id);
                if ($vendor->status !== 'active') {
                    return response()->json(['status' => false, 'message' => 'Vendor is not active'], Response::HTTP_BAD_REQUEST);
                }
                $query->where('vendor_id', $request->vendor_id);
            }

            if ($request->has('category_id')) {
                $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('id', $request->category_id);
                });
            }

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
                    case 'most_demanded':
                        $query->whereIn('id', Promotion::where('status', 'approved')
                            ->whereDate('end_date', '>=', now())
                            ->pluck('product_id'));
                        break;
                }
            }

            $cacheKey = 'products_' . md5(json_encode($request->all()));

            return $this->getCachedProducts($query, $cacheKey);
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
    |> Store a new product
    |==========================================
    */
    public function store(ProductStoreRequest $request)
    {
        try {
            $this->authorize('create', Product::class);

            $validatedData = $request->validated();

            $vendor = auth()->user()->vendors()->first();

            $product = Product::create([
                'vendor_id' => $vendor->id,
                'product_name' => $validatedData['product_name'],
                'description' => $validatedData['description'],
            ]);

            if (isset($validatedData['categories'])) {
                $product->categories()->sync($validatedData['categories']);
            }

            if (isset($validatedData['details'])) {
                foreach ($validatedData['details'] as $key => $detail) {
                    $productDetail = $product->details()->create($detail);

                    if ($request->hasFile("details.$key.images")) {
                        foreach ($request->file("details.$key.images") as $image) {
                            $productDetail->addMedia($image)->toMediaCollection('product_images');
                        }
                    }
                }
            }

            Cache::forget('products_');
            Cache::forget('promoted_products_');
            Cache::forget('most_demanded_products_');

            return response()->json([
                'status' => true,
                'data' => new ProductResource($product->loadMissing(['details', 'categories'])),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized ' . $e->getMessage()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'. $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /*
    |==========================================
    |> Update an existing product
    |==========================================
    */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        try {
            $this->authorize('update', $product);

            $validatedData = $request->validated();

            $product->update([
                'product_name' => $validatedData['product_name'] ?? $product->product_name,
                'description' => $validatedData['description'] ?? $product->description,
            ]);

            if (isset($validatedData['categories'])) {
                $product->categories()->sync($validatedData['categories']);
            }

            if (isset($validatedData['details'])) {
                foreach ($validatedData['details'] as $key => $detailData) {
                    if (isset($detailData['id'])) {
                        $productDetail = $product->details()->findOrFail($detailData['id']);
                        $productDetail->update($detailData);
                    } else {
                        $productDetail = $product->details()->create($detailData);
                    }

                    if ($request->hasFile("details.$key.images")) {
                        foreach ($request->file("details.$key.images") as $image) {
                            $productDetail->addMedia($image)->toMediaCollection('product_images');
                        }
                    }
                }
            }

            Cache::forget('products_');
            Cache::forget('promoted_products_');
            Cache::forget('most_demanded_products_');

            return response()->json([
                'status' => true,
                'data' => new ProductResource($product->fresh()),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'. $e->getMessage()], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /*
    |==========================================
    |> Show a single product
    |==========================================
    */
    public function show(Product $product)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => new ProductResource($product->loadMissing(['details', 'categories', 'vendor', 'reviews'])),
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
    |> Delete a product
    |==========================================
    */
    public function destroy(Product $product)
    {
        try {
            $this->authorize('delete', $product);

            $product->delete();
            Cache::forget('products_');
            Cache::forget('promoted_products_');
            Cache::forget('most_demanded_products_');

            return response()->json([
                'status' => true,
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
    |> Get promoted products
    |==========================================
    */
    public function getPromotedProducts()
    {
        try {
            $query = Product::whereIn('id', Promotion::where('status', 'approved')
                ->whereDate('end_date', '>=', now())
                ->pluck('product_id'))
                ->with(['details', 'vendor', 'categories']);

            $cacheKey = 'promoted_products_' . md5(json_encode(request()->all()));

            return $this->getCachedProducts($query, $cacheKey);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /*
    |==========================================
    |> Get most demanded products
    |==========================================
    */
    public function getMostDemandedProducts()
    {
        try {
            $query = Product::whereIn('id', Promotion::where('status', 'approved')
                ->whereDate('end_date', '>=', now())
                ->pluck('product_id'))
                ->orWhereHas('orders', function ($q) {
                    $q->orderByDesc('quantity');
                })
                ->with(['details', 'vendor', 'categories']);

            $cacheKey = 'most_demanded_products_' . md5(json_encode(request()->all()));

            return $this->getCachedProducts($query, $cacheKey);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /*
    |==========================================
    |> Private function to get products by category
    |==========================================
    */
    private function getCachedProducts($query, $cacheKey)
    {
        try {
            if (Cache::has($cacheKey)) {
                $products = Cache::get($cacheKey);
            } else {
                $products = $query->paginate();
                Cache::put($cacheKey, $products, now()->addMinutes(5));
            }

            return response()->json([
                'status' => true,
                'data' => ProductResource::collection($products),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}

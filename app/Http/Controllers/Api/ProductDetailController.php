<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductDetail;
use App\Models\Product;
use App\Http\Resources\ProductDetailResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Access\AuthorizationException;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\ProductDetailStoreRequest;
use App\Http\Requests\ProductDetailUpdateRequest;

class ProductDetailController extends Controller
{
    use AuthorizesRequests;
    /*
    |==========================================
    |> Get all product details with filtering and caching
    |==========================================
    */
    public function index(Request $request, $productId)
    {
        try {
            $product = Product::findOrFail($productId);

            $query = $product->details();
            if ($request->has('size')) {
                $query->where('size', $request->size);
            }

            if ($request->has('color')) {
                $query->where('color', $request->color);
            }

            if ($request->has('price_range')) {
                [$minPrice, $maxPrice] = explode(',', $request->price_range);
                $query->whereBetween('price', [(float)$minPrice, (float)$maxPrice]);
            }
            $cacheKey = 'product_details_' . $productId . '_' . md5(json_encode($request->all()));

            if (Cache::has($cacheKey)) {
                $details = Cache::get($cacheKey);
            } else {
                $details = $query->paginate();
                Cache::put($cacheKey, $details, now()->addMinutes(5));
            }

            return response()->json([
                'status' => true,
                'data' => ProductDetailResource::collection($details),
                'pagination' => [
                    'total' => $details->total(),
                    'per_page' => $details->perPage(),
                    'current_page' => $details->currentPage(),
                    'last_page' => $details->lastPage(),
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
    |> Store a new product detail
    |==========================================
    */
    public function store(ProductDetailStoreRequest $request, $productId)
    {
        try {
            $this->authorize('create', ProductDetail::class);

            $product = Product::findOrFail($productId);

            $vendor = auth()->user()->vendors()->first();
            if (!$vendor || $vendor->status !== 'active') {
                return response()->json(['status' => false, 'message' => 'Vendor is not active'], Response::HTTP_BAD_REQUEST);
            }

            $validatedData = $request->validated();

            $detail = $product->details()->create($validatedData);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $detail->addMedia($image)->toMediaCollection('product_images');
                }
            }

            Cache::forget('product_details_' . $productId);

            return response()->json([
                'status' => true,
                'data' => new ProductDetailResource($detail),
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
    |> Update an existing product detail
    |==========================================
    */
    public function update(ProductDetailUpdateRequest $request, $productId, ProductDetail $detail)
    {
        try {
            $this->authorize('update', $detail);

            $product = Product::findOrFail($productId);

            $validatedData = $request->validated();

            $detail->update($validatedData);

            if ($request->hasFile('images')) {
                $detail->clearMediaCollection('product_images');
                foreach ($request->file('images') as $image) {
                    $detail->addMedia($image)->toMediaCollection('product_images');
                }
            }

            Cache::forget('product_details_' . $productId);

            return response()->json([
                'status' => true,
                'data' => new ProductDetailResource($detail->fresh()),
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
    |> Show a single product detail
    |==========================================
    */
    public function show($productId, ProductDetail $detail)
    {
        try {
            $product = Product::findOrFail($productId);

            return response()->json([
                'status' => true,
                'data' => new ProductDetailResource($detail),
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
    |> Delete a product detail
    |==========================================
    */
    public function destroy($productId, ProductDetail $detail)
    {
        try {
            $this->authorize('delete', $detail);

            $product = Product::findOrFail($productId);

            $detail->delete();

            Cache::forget('product_details_' . $productId);

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
}

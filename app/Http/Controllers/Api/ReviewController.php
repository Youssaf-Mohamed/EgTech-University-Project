<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\ReviewResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Access\AuthorizationException;
use Exception;

class ReviewController extends Controller
{
    use AuthorizesRequests;

    /*
    |==========================================
    |> Get all reviews for a specific product with filtering and caching
    |==========================================
    */
    public function index(Product $product, Request $request)
    {
        try {
            $cacheKey = 'product_reviews_' . $product->id . '_' . md5(json_encode($request->all()));

            if (Cache::has($cacheKey)) {
                $reviews = Cache::get($cacheKey);
            } else {
                $query = $product->reviews();

                if ($request->has('search')) {
                    $query->where('comment', 'LIKE', "%{$request->search}%");
                }

                $reviews = $query->paginate();
                Cache::put($cacheKey, $reviews, now()->addMinutes(5));
            }

            return response()->json([
                'status' => true,
                'data' => ReviewResource::collection($reviews),
                'pagination' => [
                    'total' => $reviews->total(),
                    'per_page' => $reviews->perPage(),
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                ],
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
    |> Store a new review for a product
    |==========================================
    */
    public function store(Request $request, Product $product)
    {
        try {
            $validatedData = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
            ]);

            $user = auth()->user();

            if ($user->reviews()->where('product_id', $product->id)->exists()) {
                return response()->json(['status' => false, 'message' => 'You have already reviewed this product'], Response::HTTP_BAD_REQUEST);
            }

            $review = $product->reviews()->create([
                'user_id' => $user->id,
                'rating' => $validatedData['rating'],
                'comment' => $validatedData['comment'] ?? null,
            ]);

            Cache::forget('product_' . $product->id);

            return response()->json([
                'status' => true,
                'data' => new ReviewResource($review),
            ]);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'. $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /*
    |==========================================
    |> Update an existing review for a product
    |==========================================
    */
    public function update(Request $request, Product $product, Review $review)
    {
        try {
            $this->authorize('update', $review);

            $validatedData = $request->validate([
                'rating' => 'nullable|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
            ]);

            $review->update($validatedData);

            Cache::forget('product_' . $product->id);

            return response()->json([
                'status' => true,
                'data' => new ReviewResource($review->fresh()),
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
    |> Delete a review for a product
    |==========================================
    */
    public function destroy(Product $product, Review $review)
    {
        try {
            $this->authorize('delete', $review);

            $review->delete();

            Cache::forget('product_' . $product->id);

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

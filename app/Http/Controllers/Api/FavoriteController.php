<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /*
    |==========================================
    |> Toggle a product in the user's favorites list.
    |==========================================
    */
    public function toggle(Product $product)
    {
        $user = Auth::user();

        // Toggle the product in the user's favorites list
        $user->favorites()->toggle($product);

        // Check if the product is currently favorited by the user
        $isFavorited = $user->favorites()->where('product_id', $product->id)->exists();

        return response()->json([
            'status' => true,
            'message' => $isFavorited ? 'Product added to favorites.' : 'Product removed from favorites.',
            'data' => [
                'is_favorited' => $isFavorited,
            ],
        ]);
    }

    /*
    |==========================================
    |> Remove a product from the user's favorites list.
    |==========================================
    */
    public function remove(Product $product)
    {
        $user = Auth::user();

        if ($user->favorites()->detach($product)) {
            return response()->json([
                'status' => true,
                'message' => 'Product removed from favorites.',
            ]);
        }

        return response()->json(['status' => false, 'message' => 'Failed to remove from favorites.'], 500);
    }

    /*
    |==========================================
    |> Get all favorite products for the authenticated user.
    |==========================================
    */
    public function index()
    {
        $user = Auth::user();
        $favoriteProducts = $user->favorites;

        return response()->json([
            'status' => true,
            'data' => $favoriteProducts,
        ]);
    }

    /*
    |==========================================
    |> Check if a product is favorited by the authenticated user.
    |==========================================
    */
    public function check(Product $product)
    {
        $user = Auth::user();

        $isFavorited = $user->favorites()->where('product_id', $product->id)->exists();

        return response()->json([
            'status' => true,
            'data' => [
                'is_favorited' => $isFavorited,
            ],
        ]);
    }
}

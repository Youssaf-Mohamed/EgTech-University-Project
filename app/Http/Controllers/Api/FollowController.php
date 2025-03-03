<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Response;

class FollowController extends Controller
{
    /*
    |==========================================
    |> Follow or unfollow a vendor
    |==========================================
    */
    public function toggleFollow(Request $request, Vendor $vendor)
    {
        try {
            $user = auth()->user();

            if ($user->followedVendors()->toggle($vendor)) {
                return response()->json([
                    'status' => true,
                    'message' => $user->followedVendors->contains($vendor) ? 'Vendor followed successfully' : 'Vendor unfollowed successfully',
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Failed to follow/unfollow vendor',
            ],  Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    |==========================================
    |> Get all vendors followed by the user
    |==========================================
    */
    public function getFollowedVendors(Request $request)
    {
        try {
            $user = auth()->user();
            $vendors = $user->followedVendors()->with('regions')->paginate(10);

            return response()->json([
                'status' => true,
                'data' => \App\Http\Resources\VendorResource::collection($vendors),
                'pagination' => [
                    'total' => $vendors->total(),
                    'per_page' => $vendors->perPage(),
                    'current_page' => $vendors->currentPage(),
                    'last_page' => $vendors->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    |==========================================
    |> Get latest activities of followed vendors
    |==========================================
    */
    public function getLatestActivities(Request $request)
    {
        try {
            $user = auth()->user();

            // Fetch the latest products or activities from followed vendors
            $activities = Product::whereIn('vendor_id', $user->followedVendors->pluck('id'))
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'status' => true,
                'data' => ProductResource::collection($activities),
                'pagination' => [
                    'total' => $activities->total(),
                    'per_page' => $activities->perPage(),
                    'current_page' => $activities->currentPage(),
                    'last_page' => $activities->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

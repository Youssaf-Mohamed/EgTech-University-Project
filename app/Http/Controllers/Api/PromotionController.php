<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Promotion;
use App\Http\Resources\PromotionResource;
use App\Models\Vendor;
use App\Http\Requests\PromotionStoreRequest;
use App\Http\Requests\PromotionUpdateRequest;
use App\Services\PromotionApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PromotionController extends Controller
{
    use AuthorizesRequests;

    /*
    |==========================================
    |> Get all promotions for admin
    |==========================================
    */
    public function index(Request $request)
    {
        try {
            return $this->getPromotions($request);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'], 500);
        }
    }

    /*
    |==========================================
    |> Get all vendor subscriptions to promotions
    |==========================================
    */
    public function vendorSubscriptions(Request $request)
    {
        try {
            $this->authorize('viewAny', Promotion::class);

            $search = $request->query('search');
            $subscriptions = Promotion::with(['vendors' => function ($query) use ($search) {
                if ($search) {
                    $query->whereHas('vendor', function ($q) use ($search) {
                        $q->where('brand_name', 'LIKE', "%$search%")
                            ->orWhere('description', 'LIKE', "%$search%");
                    });
                }
            }])
                ->whereHas('vendors')
                ->paginate();

            return response()->json([
                'status' => true,
                'data' => PromotionResource::collection($subscriptions),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'], 500);
        }
    }

    /*
    |==========================================
    |> Store a new promotion
    |==========================================
    */
    public function store(PromotionStoreRequest $request)
    {
        try {
            $this->authorize('create', Promotion::class);

            $promotion = Promotion::create($request->validated());
            return response()->json([
                'status' => true,
                'data' => new PromotionResource($promotion),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'], 500);
        }
    }

    /*
    |==========================================
    |> Show a single promotion
    |==========================================
    */
    public function show(Promotion $promotion)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => new PromotionResource($promotion->load('vendors')),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Promotion not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'], 500);
        }
    }

    /*
    |==========================================
    |> Update an existing promotion
    |==========================================
    */
    public function update(PromotionUpdateRequest $request, Promotion $promotion)
    {
        try {
            $this->authorize('update', $promotion);

            $promotion->update($request->validated());
            return response()->json([
                'status' => true,
                'data' => new PromotionResource($promotion->fresh()),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'], 500);
        }
    }

    /*
    |==========================================
    |> Vendor subscribes to a promotion
    |==========================================
    */
    public function subscribe(Vendor $vendor, Promotion $promotion)
    {
        try {
            $this->authorize('subscribe', $promotion);

            $vendor->promotions()->attach($promotion->id, [
                'start_date' => now(),
                'end_date' => now()->addDays(value: $promotion->duration),
                'status' => 'pending',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Subscription request sent successfully.',
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'. $e->getMessage()], 403);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'. $e->getMessage()], 500);
        }
    }

    /*
    |==========================================
    |> Approve a vendor's subscription to a promotion
    |==========================================
    */
    public function approveSubscription(Vendor $vendor, Promotion $promotion, PromotionApprovalService $approvalService)
    {
        try {
            $this->authorize('update', $promotion);

            $pivot = $vendor->promotions()->wherePivot('promotion_id', $promotion->id)->first();
            if (!$pivot) {
                return response()->json(['status' => false, 'message' => 'No subscription found.'], 404);
            }

            $vendor->promotions()->updateExistingPivot($promotion->id, [
                'status' => 'approved',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Subscription approved successfully.',
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'. $e->getMessage()], 500);
        }
    }

    /*
    |==========================================
    |> Reject a vendor's subscription to a promotion
    |==========================================
    */
    public function rejectSubscription(Vendor $vendor, Promotion $promotion, PromotionApprovalService $approvalService)
    {
        try {
            $this->authorize('update', $promotion);

            $pivot = $vendor->promotions()->wherePivot('promotion_id', $promotion->id)->first();
            if (!$pivot) {
                return response()->json(['status' => false, 'message' => 'No subscription found.'], 404);
            }

            $vendor->promotions()->updateExistingPivot($promotion->id, [
                'status' => 'rejected',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Subscription rejected successfully.',
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'. $e->getMessage()], 500);
        }
    }


    /*
    |==========================================
    |> Delete a promotion
    |==========================================
    */
    public function destroy(Promotion $promotion)
    {
        try {
            $this->authorize('delete', $promotion);

            $promotion->delete();

            return response()->json([
                'status' => true,
                'message' => 'Promotion deleted successfully.',
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Promotion not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'], 500);
        }
    }

    /*
    |==========================================
    |> Get all promotions
    |==========================================
    */
    private function getPromotions(Request $request, $onlyActive = false)
    {
        $search = $request->query('search');
        $query = Promotion::with('vendors');

        if ($onlyActive) {
            $query->where('status', 'active');
        }

        $promotions = $query
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%$search%");
            })
            ->paginate();

        return response()->json([
            'status' => true,
            'data' => PromotionResource::collection($promotions),
            'pagination' => [
                'total' => $promotions->total(),
                'per_page' => $promotions->perPage(),
                'current_page' => $promotions->currentPage(),
                'last_page' => $promotions->lastPage(),
            ],
        ]);
    }
}

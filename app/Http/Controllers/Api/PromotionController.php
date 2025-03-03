<?php

namespace App\Http\Controllers\Api;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Vendor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;
use App\Services\PromotionApprovalService;
use App\Http\Resources\PromotionResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\PromotionSubscribeRequest;
use App\Http\Requests\PromotionUpdateRequest;
use App\Http\Requests\PromotionStoreRequest;

class PromotionController extends Controller
{
    use AuthorizesRequests;

    /*
    |==========================================
    |> Get all promotions
    |==========================================
    */
    public function index(Request $request)
    {
        try {
            $promotions = Promotion::all();

            return response()->json([
                'status' => true,
                'data' => PromotionResource::collection($promotions)
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /*
    |==========================================
    |> Create a new promotion
    |==========================================
    */
    public function create(PromotionStoreRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $promotion = Promotion::create($validatedData);

            return response()->json([
                'status' => true,
                'data' => new PromotionResource($promotion),
            ]);
        } catch (\Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
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
            $validatedData = $request->validated();

            $promotion->update($validatedData);

            return response()->json([
                'status' => true,
                'data' => new PromotionResource($promotion),
            ]);
        } catch (\Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
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
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /*
    |==========================================
    |>  subscribe to a promotion
    |==========================================
    */
    public function subscribe(PromotionSubscribeRequest $request, Promotion $promotion)
    {
        try {
            $vendor = auth()->user()->vendors()->first();

            if (!$vendor) {
                return response()->json(['status' => false, 'message' => 'Vendor not found'], Response::HTTP_NOT_FOUND);
            }

            $validatedData = $request->validated();

            $endDate = now()->addDays($promotion->duration);

            $vendor->promotions()->attach($promotion->id, [
                'start_date' => $validatedData['start_date'],
                'end_date' => $endDate,
                'status' => 'pending',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Subscription request created successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /*
    |==========================================
    |> approve Subscription
    |==========================================
    */
    public function approveSubscription(Vendor $vendor, Promotion $promotion, PromotionApprovalService $approvalService)
    {
        try {
            $this->authorize('approve', $promotion);

            $subscription = $vendor->promotions()->wherePivot('promotion_id', $promotion->id)->first();

            if ($subscription) {
                $approvalService->approve($subscription->pivot);
            }

            return response()->json([
                'status' => true,
                'message' => 'Subscription approved successfully',
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
    |> reject Subscription
    |==========================================
    */
    public function rejectSubscription(Vendor $vendor, Promotion $promotion, PromotionApprovalService $approvalService)
    {
        try {
            $this->authorize('reject', $promotion);

            $subscription = $vendor->promotions()->wherePivot('promotion_id', $promotion->id)->first();

            if ($subscription) {
                $approvalService->reject($subscription->pivot);
            }

            return response()->json([
                'status' => true,
                'message' => 'Subscription rejected successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

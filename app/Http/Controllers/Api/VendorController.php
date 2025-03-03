<?php

namespace App\Http\Controllers\Api;

use App\Models\Vendor;
use App\Http\Controllers\Controller;
use App\Http\Resources\VendorResource;
use App\Http\Requests\VendorStoreRequest;
use App\Http\Requests\VendorUpdateRequest;
use App\Services\VendorApprovalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

class VendorController extends Controller
{
    use AuthorizesRequests;

    /*
    |==========================================
    |> Get all vendors for admin
    |==========================================
    */
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', Vendor::class);
            return $this->getVendors($request);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /*
    |==========================================
    |> Get all vendors
    |==========================================
    */
    public function publicIndex(Request $request)
    {
        try {
            return $this->getVendors($request, true);
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
    |> Store a new vendor
    |==========================================
    */
    public function store(VendorStoreRequest $request)
    {
        try {
            $this->authorize('create', Vendor::class);

            $vendor = Vendor::create($request->validated());
            $vendor->users()->attach(auth()->id());
            $vendor->regions()->sync($request->regions);

            if ($request->hasFile('vendor_images')) {
                $vendor->addMediaFromRequest('vendor_images')->toMediaCollection('vendor_images');
            }
            return response()->json([
                'status' => true,
                'data' => new VendorResource($vendor->Load(['users', 'regions'])),
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
    |> Show a single vendor
    |==========================================
    */
    public function show(Vendor $vendor)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => new VendorResource($vendor->load(['users', 'regions'])),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => false,],Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error '],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /*
    |==========================================
    |> Update an existing vendor
    |==========================================
    */
    public function update(VendorUpdateRequest $request, Vendor $vendor)
    {
        try {
            $this->authorize('update', $vendor);

            $vendor->update($request->validated());

            if ($request->has('regions')) {
                $vendor->regions()->sync(collect($request->regions)->mapWithKeys(function ($region) {
                    return [$region['region_id'] => [
                        'delivery_cost' => $region['delivery_cost'] ?? null,
                        'discount' => $region['discount'] ?? null,
                        'description' => $region['description'] ?? null,
                        'updated_at' => now()
                    ]];
                }));
            }


            if ($request->hasFile('vendor_images')) {
                $vendor->clearMediaCollection('vendor_images');
                $vendor->addMediaFromRequest('vendor_images')->toMediaCollection('vendor_images');
            }
            return response()->json([
                'status' => true,
                'data' => new VendorResource($vendor->fresh()),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    |==========================================
    |> Delete a vendor
    |==========================================
    */
    public function destroy(Vendor $vendor)
    {
        try {
            $this->authorize('delete', $vendor);
            $vendor->delete();
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
    |> Approve a vendor
    |==========================================
    */
    public function approve(Vendor $vendor, VendorApprovalService $approvalService)
    {
        try {
            $this->authorize('approve', $vendor);

            $approvalService->approve($vendor);
            return response()->json([
                'status' => true,
                'data' => new VendorResource($vendor),
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
    |> Reject a vendor
    |==========================================
    */
    public function reject(Vendor $vendor, VendorApprovalService $approvalService)
    {
        try {
            $this->authorize('reject', $vendor);

            $approvalService->reject($vendor);
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
    |> Get all soft-deleted vendors
    |==========================================
    */
    public function trashedVendors(Request $request)
    {
        try {
            $this->authorize('viewAny', Vendor::class);

            // Retrieve query parameters for filtering
            $search = $request->query('search');

            // Fetch soft-deleted vendors with optional filtering
            $vendors = Vendor::onlyTrashed()
                ->with(['users', 'regions'])
                ->when($search, function ($query, $search) {
                    return $query->where('brand_name', 'LIKE', "%$search%")
                        ->orWhere('description', 'LIKE', "%$search%");
                })
                ->paginate();

            // return VendorResource::collection($vendors);
            return response()->json([
                'status' => true,
                'data' => VendorResource::collection($vendors),
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
    |> Restore a deleted vendor
    |==========================================
    */
    public function restore($id)
    {
        try {
            $vendor = Vendor::onlyTrashed()->findOrFail($id);
            $this->authorize('restore', $vendor);

            $vendor->restore();
            // return new VendorResource($vendor);
            return response()->json([
                'status' => true,
                'data' => new VendorResource($vendor),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        } catch (ModelNotFoundException $e) {
            return response()->json(
                ['status' => false, 'message' => 'Vendor not found'],
                Response::HTTP_NOT_FOUND
            );
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


    /*
    |==========================================
    |> Get all vendor
    |==========================================
    */
    private function getVendors(Request $request, $onlyActive = false)
    {
        $search = $request->query('search');
        $query = Vendor::with(['users', 'regions']);

        if ($onlyActive) {
            $query->where('status', 'active');
        }

        $vendors = $query
            ->when($search, function ($query, $search) {
                return $query->where('brand_name', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
            })
            ->paginate();

        return response()->json([
            'status' => true,
            'data' => VendorResource::collection($vendors),
            'pagination' => [
                'total' => $vendors->total(),
                'per_page' => $vendors->perPage(),
                'current_page' => $vendors->currentPage(),
                'last_page' => $vendors->lastPage(),
            ],
        ]);
    }
}

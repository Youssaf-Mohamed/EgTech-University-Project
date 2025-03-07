<?php

namespace App\Http\Controllers\Api;

use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\AdvertisementResource;

class AdvertisementController extends Controller
{
    use AuthorizesRequests;

    /*
    /*
    |--------------------------------------------------------------------------
    | Get all advertisements
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        try {
            $query = Advertisement::query();

            $advertisements = $query->get();

            return response()->json([
                'status' => true,
                'data' => AdvertisementResource::collection($advertisements)
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Store a new advertisement
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        try {
            // Authorization check
            $this->authorize('create', Advertisement::class);

            $request->validate([
                'title' => 'nullable|string|max:255',
                'brief' => 'nullable|string',
                'content' => 'nullable|string',
                'redirect_to' => 'nullable|string',
                'target_screen' => 'nullable|string',
            ]);

            $advertisement = Advertisement::create($request->all());

            if ($request->hasFile('advertisement_image')) {
                $advertisement->clearMediaCollection('advertisement_image');
                $advertisement->addMediaFromRequest('advertisement_image')->toMediaCollection('advertisement_image');
            }

            return response()->json([
                'status' => true,
                'data' => new AdvertisementResource($advertisement),
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Show a single advertisement
    |--------------------------------------------------------------------------
    */
    public function show(Advertisement $advertisement)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => new AdvertisementResource($advertisement),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Advertisement not found'. $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update an existing advertisement
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Advertisement $advertisement)
    {
        try {
            // Authorization check
            $this->authorize('update', $advertisement);

            $request->validate([
                'title' => 'nullable|string|max:255',
                'brief' => 'nullable|string',
                'content' => 'nullable|string',
                'redirect_to' => 'nullable|string',
                'target_screen' => 'nullable|string',
            ]);

            $advertisement->update($request->all());

            if ($request->hasFile('advertisement_image')) {
                $advertisement->clearMediaCollection('advertisement_image');
                $advertisement->addMediaFromRequest('advertisement_image')->toMediaCollection('advertisement_image');
            }

            return response()->json([
                'status' => true,
                'data' => new AdvertisementResource($advertisement),
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete an advertisement
    |--------------------------------------------------------------------------
    */
    public function destroy(Advertisement $advertisement)
    {
        try {
            // Authorization check
            $this->authorize('delete', $advertisement);

            $advertisement->delete();

            return response()->json([
                'status' => true
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

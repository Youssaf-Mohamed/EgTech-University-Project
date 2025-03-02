<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Region;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RegionController extends Controller
{
    use AuthorizesRequests;

    /*
    |==========================================
    |> Get all regions
    |==========================================
    */
    public function index()
    {
        try {
            $regions = Region::all();

            return response()->json([
                'status' => true,
                'data' => $regions,
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
    |> Store a new region
    |==========================================
    */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', Region::class);

            $validatedData = $request->validate([
                'name' => 'required|string|unique:regions,name',
            ]);

            $region = Region::create($validatedData);

            return response()->json([
                'status' => true,
                'data' => $region,
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
    |> Show a single region
    |==========================================
    */
    public function show(Region $region)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => $region,
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
    |> Update an existing region
    |==========================================
    */
    public function update(Request $request, Region $region)
    {
        try {
            $this->authorize('update', $region);

            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|unique:regions,name,' . $region->id,
            ]);

            $region->update($validatedData);

            return response()->json([
                'status' => true,
                'data' => $region,
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
    |> Delete a region
    |==========================================
    */
    public function destroy(Region $region)
    {
        try {
            $this->authorize('delete', $region);

            $region->delete();

            return response()->json([
                'status' => true,
                'message' => 'Region deleted successfully'
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        } catch (ModelNotFoundException $e) {
            return response()->json(
                ['status' => false, 'message' => 'Region not found'],
                Response::HTTP_NOT_FOUND
            );
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}

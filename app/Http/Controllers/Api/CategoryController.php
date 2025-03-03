<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /*
    |==========================================
    |> Get all categories for admin or public
    |==========================================
    */
    public function index(Request $request)
    {
        try {
            return $this->getCategories($request);
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
    |> Store a new category (Admin only)
    |==========================================
    */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', Category::class);

            $validatedData = $request->validate([
                'category_name' => 'required|string|unique:categories,category_name',
                'description' => 'nullable|string',
            ]);

            $category = Category::create($validatedData);

            if ($request->hasFile('image')) {
                $category->addMediaFromRequest('image')->toMediaCollection('category_images');
            }

            return response()->json([
                'status' => true,
                'data' => new CategoryResource($category),
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
    |> Show a single category
    |==========================================
    */
    public function show(Category $category)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => new CategoryResource($category),
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
    |> Update an existing category (Admin only)
    |==========================================
    */
    public function update(Request $request, Category $category)
    {
        try {
            $this->authorize('update', $category);

            $validatedData = $request->validate([
                'category_name' => 'sometimes|required|string|unique:categories,category_name,' . $category->id,
                'description' => 'nullable|string',
            ]);

            $category->update($validatedData);

            if ($request->hasFile('image')) {
                $category->clearMediaCollection('category_images');
                $category->addMediaFromRequest('image')->toMediaCollection('category_images');
            }

            return response()->json([
                'status' => true,
                'data' => new CategoryResource($category),
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
    |> Delete a category (Admin only)
    |==========================================
    */
    public function destroy(Category $category)
    {
        try {
            $this->authorize('delete', $category);

            $category->delete();

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
    |> Get all categories with pagination and search
    |==========================================
    */
    private function getCategories(Request $request)
    {
        $search = $request->query('search');
        $query = Category::query();

        $categories = $query
            ->when($search, function ($query, $search) {
                return $query->where('category_name', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
            })
            ->paginate();

        return response()->json([
            'status' => true,
            'data' => CategoryResource::collection($categories),
            'pagination' => [
                'total' => $categories->total(),
                'per_page' => $categories->perPage(),
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
            ],
        ]);
    }
}

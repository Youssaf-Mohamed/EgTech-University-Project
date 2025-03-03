<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\Gender;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Exception;
use App\Notifications\AccountVerificationNotification;

class UserController extends Controller
{
    use AuthorizesRequests;

    /*
    |==========================================
    |> Get all users
    |==========================================
    */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%");
            });
        }

        if ($request->has('role')) {
            $query->where('role', $request->input('role'));
        }
        if ($request->has('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate($request->input('per_page', 10));

        return response()->json([
            'status' => true,
            'data' => UserResource::collection($users),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    /*
    |==========================================
    |> Get authenticated user's profile
    |==========================================
    */
    public function show(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'statusCode' => Response::HTTP_OK,
                'status' => true,
                'data' => new UserResource($user)
            ]);
        } catch (\Exception $e) {
            return response()->json(['statusCode' => Response::HTTP_NOT_FOUND, 'status' => false]);
        }
    }
    /*
    |==========================================
    |> Get authenticated user's profile
    |==========================================
    */
    public function currentUser(Request $request)
    {
        try {
            return response()->json([
                'statusCode' => Response::HTTP_OK,
                'status' => true,
                'data' => new UserResource($request->user())
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    |==========================================
    |> Update user profile
    |==========================================
    */
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $request->user()->id,
                'password' => 'sometimes|string|min:8|confirmed',
                'address' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:15|unique:users,phone,' . $request->user()->id,
                'gender' => 'sometimes|in:' . implode(',', Gender::getValues()),
                'is_active' => 'sometimes|boolean',
                'role' => 'sometimes|in:user,vendor,admin',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user = $request->user();

            if ($request->hasFile('profile_picture')) {
                $user->clearMediaCollection('profile_pictures');
                $user->addMediaFromRequest('profile_picture')->toMediaCollection('profile_pictures');
            }

            $user->update($request->only('name', 'email', 'address', 'phone', 'gender', 'is_active', 'role') + [
                'password' => $request->has('password') ? Hash::make($request->password) : $user->password,
            ]);

            return new UserResource($user);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    |==========================================
    |> Register a new user
    |==========================================
    */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:15|unique:users',
                'gender' => 'required|in:' . implode(',', Gender::getValues()),
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'verification_token' => Str::random(64),
            ]);

            if ($request->hasFile('profile_picture')) {
                $user->addMediaFromRequest('profile_picture')->toMediaCollection('profile_pictures');
            }
            $user->notify(new AccountVerificationNotification($user));

            return response()->json([
                'status' => true,
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $user->createToken('auth_token')->plainTextToken,
                ],
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    |==========================================
    |> User login
    |==========================================
    */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['status' => false, 'message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $user->createToken('auth_token')->plainTextToken,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    |==========================================
    |> User logout
    |==========================================
    */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['status' => true]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    |==========================================
    |> Delete the user and associated media.
    |==========================================
    */
    public function destroy(Request $request, $id)
    {
        $user = User::find($id);
        $this->authorize('delete', $user);

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if (Auth::user()->role !== 'admin' && Auth::user()->id !== $user->id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $user->clearMediaCollection('profile_pictures');
        $user->delete();

        return response()->json(['status' => true, 'message' => 'User deleted successfully']);
    }

    /*
    |==========================================
    |>  verify user email
    |==========================================
    */
    public function verifyAccount($token)
    {
        try {
            $user = User::where('verification_token', $token)->first();

            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Invalid verification token'], Response::HTTP_BAD_REQUEST);
            }

            $user->update([
                'verification_token' => null,
                'is_verified' => true,
                'is_active' => true,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Your account has been verified successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}

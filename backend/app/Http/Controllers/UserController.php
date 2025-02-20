<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\Gender; // Add this
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Get authenticated user's profile (using Resource)
    public function show(Request $request)
    {
        return new UserResource($request->user());
    }

    // Update profile
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$request->user()->id,
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'gender' => 'sometimes|in:'.implode(',', Gender::getValues()), // Validate enum
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Handle profile picture
        if ($request->hasFile('profile_picture')) {
            $user->clearMediaCollection('profile_pictures');
            $user->addMediaFromRequest('profile_picture')
                ->toMediaCollection('profile_pictures');
        }

        // Update user
        $user->update([
            'name' => $request->input('name', $user->name),
            'email' => $request->input('email', $user->email),
            'gender' => $request->input('gender', $user->gender),
            'password' => $request->has('password') 
                ? Hash::make($request->password) 
                : $user->password,
        ]);

        return new UserResource($user);
    }

    // Register
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'gender' => 'required|in:'.implode(',', Gender::getValues()), // Add gender validation
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
            'bio' => $request->bio,
        ]);

        if ($request->hasFile('profile_picture')) {
            $user->addMediaFromRequest('profile_picture')
                ->toMediaCollection('profile_pictures');
        }

        return response()->json([
            'status' => true,
            'data' => [
                'user' => new UserResource($user),
                'token' => $user->createToken('auth_token')->plainTextToken,
            ],
        ], 201);
    }

    // Login (improved security)
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401); // Generic message for security
        }

        return response()->json([
            'status' => true,
            'data' => [
                'user' => new UserResource($user),
                'token' => $user->createToken('auth_token')->plainTextToken,
            ],
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => true]);
    }

    // Delete user
    public function destroy(Request $request)
    {
        $user = $request->user();
        $user->clearMediaCollection('profile_pictures');
        $user->delete();
        return response()->json(['status' => true]);
    }
}
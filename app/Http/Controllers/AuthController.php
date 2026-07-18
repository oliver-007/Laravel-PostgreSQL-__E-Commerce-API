<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Helper method to generate a secure production-grade HttpOnly cookie.

    private function generateTokenCookie(string $token)
    {
        return Cookie(
            'access_token',
            $token,
            120,
            '/',
            config('session.domain'),
            config('session.secure'),
            true,
            false,
            'lax'
        );
    }

    // User Registration
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate a Sanctum token for this specific user session
        $token = $user->createToken('auth_token')->plainTextToken;
        $cookie = $this->generateTokenCookie($token);

        // Return the user payload alongside the Bearer token
        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => new UserResource($user),
            ],

        ], 201)->withCookie($cookie);

    }

    public function login(LoginRequest $request): JsonResponse
    {
        // Find the user by mail
        $user = User::query()->where('email', $request->email)->first();

        // Check if user exists and the passwrod is correct
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials provided',
            ], 401);
        }

        // Generate a new token for this session
        $token = $user->createToken('auth_token')->plainTextToken;
        $cookie = $this->generateTokenCookie($token);

        // Return success response with data
        return response()->json([
            'success' => true,
            'message' => 'Login Successful',
            'data' => [
                'user' => new UserResource($user),
            ],
        ], 200)->withCookie($cookie);
    }

    public function logout(Request $request): JsonResponse
    {
        // Revoke the token record inside the database

        $request->user()->currentAccessToken()->delete();

        $forgetCookie = Cookie::forget('access_token');

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out. Token revoked.',
        ], 200)->withCookie($forgetCookie);
    }
}

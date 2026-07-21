<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
// use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

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

        $token = $user->createToken('auth_token')->plainTextToken;

        // ---------- Token Approach ----------
        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'data' => [
                'user' => new UserResource($user),
            ],
        ], 201);

        // ------ Cookie Approach -----------

        // $cookie = $this->generateTokenCookie($token);

        // // Return the user payload alongside the Bearer token
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Registration successful',
        //     'data' => [
        //         'user' => new UserResource($user),
        //     ],

        // ], 201)->withCookie($cookie);

    }

    public function login(LoginRequest $request): JsonResponse
    {
        // Find the user by mail
        $user = User::query()->where('email', $request->email)->first();
        $verifiedPassword = Hash::check($request->password, $user->password);
        // Check if user exists and the passwrod is correct
        if (! $user || ! $verifiedPassword) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials provided',
            ], 401);
        }

        // Generate a new token for this session
        $token = $user->createToken('auth_token')->plainTextToken;

        // -------------- Token Approach --------------
        return response()->json([
            'success' => true,
            'message' => 'Login Successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'data' => [
                'user' => new UserResource($user),
            ],
        ], 200);

        // ------------- Cookie Approach -------------

        // $cookie = $this->generateTokenCookie($token);

        // // Return success response with data
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Login Successful',
        //     'data' => [
        //         'user' => new UserResource($user),
        //     ],
        // ], 200)->withCookie($cookie);

    }

    public function logout(Request $request): JsonResponse
    {
<<<<<<< HEAD

        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
=======
        // Revoke the token record inside the database
        $user = $request->user();

        if (! $user) {
            $cookieToken = $request->cookie('access_token');
            if ($cookieToken) {
                $cleanToken = urldecode($cookieToken);
                $accessToken = PersonalAccessToken::findToken($cleanToken);
                if ($accessToken) {
                    $user = $accessToken->tokenable;
                    $accessToken->delete();
                }
            }

        } else {
            $request->user()->currentAccessToken()->delete();
>>>>>>> 3b21b59f4a9cf7d24971ce885aef9cbe15150853
        }

        if ($request->boolean('all_devices')) {
            $user->tokens()->delete();
            $message = 'Successfully logged out from all devices.';

        } else {
            $currentToken = $user->currentAccessToken();
            if ($currentToken && method_exists($currentToken, 'delete')) {
                $currentToken->delete();
            }
            $message = 'Successfully logged out. ';
        }

        // ---------- Token Approach  -----------
        return response()->json([
            'success' => true,
            'message' => $message,
        ], 200);

        // -------------- Cookie Approach ----------------

        // $forgetCookie = Cookie::forget('access_token');
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Successfully logged out. Token revoked.',
        // ], 200)->withCookie($forgetCookie);

    }

    public function me(Request $request): JsonResponse
    {

        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated or session expired.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fetched user details successfully',
            'data' => [
                'user' => new UserResource($user),
            ],
        ], 200);
    }

    public function me(Request $request): JsonResponse
    {

        return response()->json([
            'success' => true,
            'message' => 'Fetched User Details Successfully ..',
            'data' => [
                'user' => new UserResource($request->user()),
            ],
        ], 200);
    }
}

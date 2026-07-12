<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate a Sanctum token for this specific user session
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return the user payload alongside the Bearer token
        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                    'user' => new UserResource($user),
                     'access_token' => $token,
            'token_type' => 'Bearer',
            ],
           
        ], 201);

    }

    public function login(LoginRequest $request):JsonResponse{
// Find the user by mail
    $user = User::query()-> where('email', $request->email)->first();

    // Check if user exists and the passwrod is correct
    if(!$user || !Hash::check($request->password, $user->password)){
        return response()->json([
            'success'=> false,
            'message'=>'Invalid credentials provided',
        ],401);
    }

    // Generate a new token for this session
    $token = $user->createToken('auth_token')->plainTextToken;

    // Return success response with data
return response()->json([
    'success'=> true,
    'message'=> "Login Successful",
    'data' => [
        'user'=> new UserResource($user),
         'access_token' => $token,
            'token_type' => 'Bearer',
    ]
]);
    }
}

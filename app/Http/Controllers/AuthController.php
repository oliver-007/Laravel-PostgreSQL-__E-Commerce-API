<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;




class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse {
$user = User::create([
    'name'=>$request->name, 
    'email'=> $request->email,
    'password'=> Hash::make($request->password),
]);

// Generate a Sanctum token for this specific user session
$token = $user->createToken('auth_token')->plainTextToken;

// Return the user payload alongside the Bearer token 
return response()->json([
'success' => true,   
'message'=>'Registration successful',
'access_token' => $token,
'token_type' => 'Bearer',
], 201);

}
}

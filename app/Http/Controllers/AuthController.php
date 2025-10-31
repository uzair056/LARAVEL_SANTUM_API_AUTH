<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\String\ByteString;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    
public function register(Request $request)
{
$validator = validator($request->all(), [
    'name' => 'required|max:255',
    'email' => 'required|email',
    'password' => 'required|min:6',
]);
if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}

$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
]);

$token = $user->createToken('authToken')->plainTextToken;

return response()->json(['user' => $user, 'token' => $token], 201);
}


public function login(Request $request)
{
    $validator = validator($request->all(), [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $token = $user->createToken('authToken')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token
    ], 200);
}


public function user(Request $request)
{

return response()->json(['user' => $request->user()], 200);


}

    public function logout(Request $request)
    {
        // Delete only current token (if using token-based Sanctum)
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }


}



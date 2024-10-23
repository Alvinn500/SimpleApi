<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => "name, email and password are required",
                'status' => 400
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'unique:users|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => "Email already exists",
                'status' => 400
            ], 400);
        }

        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string',
        ]);

        $user = User::create($fields);

        $token = $user->createToken($request->name)->plainTextToken;

        return response()->json([
            "message" => "User has been created",
            "data" => $user,
            "token" => $token,
            "status" => 201
        ], 201);
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => "email and password are required",
                'status' => 400
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'exists:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => "The email or password is doesn't exist",
                'status' => 401
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                "message" => "The email or password is doesn't exist.",
                "status" => 401
            ], 401);
        }

        $token = $user->createToken($user->name)->plainTextToken;

        return response()->json([
            "message" => "User has been logged in",
            "data" => $user,
            "token" => $token,
            "status" => 200
        ], 200)->cookie('token', $token, 60 * 24);
    }

    public function logout(Request $request)
    {

        $request->user()->tokens()->delete();

        return response()->json([
            "message" => "User has been logged out",
            "status" => 200
        ], 200);
    }
}

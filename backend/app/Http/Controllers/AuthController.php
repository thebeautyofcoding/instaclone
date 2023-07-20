<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);
        // encrypt password
        $validatedData['password'] = bcrypt($request->password);
        // create user
        $user = User::create($validatedData);
        // create token
        $succes['token'] = $user->createToken('InstaClone')->plainTextToken;
        $succes['user'] = new UserResource($user);
        return response()->json([
            'user' =>
            $succes['user'],
            'token' => $succes['token']
        ], 201);
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        if (!auth()->attempt($validator->validated())) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
        $user = auth()->user();
        $succes['token'] = $user->createToken('InstaClone')->plainTextToken;
        $succes['user'] = new UserResource($user);
        return response()->json([
            'user' =>
            $succes['user'],
            'token' => $succes['token']
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out'
        ], 200);
    }
}

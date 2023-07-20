<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function showUserData(Request $request, User $user)
    {
        // check if valid user
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        // return user
        return response()->json([
            'user' => $user
        ], 200);
    }


    public function showUserPosts(Request $request, User $user)
    {
        // check if valid user
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        // return user
        return response()->json([
            'posts' => $user->posts()->with(['user'])->latest()->get()
        ], 200);
    }
}

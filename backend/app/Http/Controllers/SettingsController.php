<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([

            'user' => $request->user(),

        ], 200);
    }
    public function update(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:255|',

        ]);

        $request->user()->update($validated);

        return response()->json([
            'user' => $request->user()
        ], 200);
    }

    public function updateAvatar(Request $request)
    {

        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // if user has already an avatar, delete it
        if ($request->user()->avatar_url && Storage::exists($request->user()->avatar_url)) {
            Storage::delete($request->user()->avatar_url);
        }
        $user = $request->user();
        $avatarName = $user->id . '_avatar' . time() . '.' . request()->avatar->getClientOriginalExtension();
        $validated['avatar']->storeAs('public/avatars', $avatarName);
        $avatarUrl = Storage::url('avatars/' . $avatarName);
        $user->avatar_url = $avatarUrl;
        $user->save();
        return response()->json([
            'user' => $request->user()
        ], 200);
    }
}

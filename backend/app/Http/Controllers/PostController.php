<?php

namespace App\Http\Controllers;

use App\Events\PostCreated;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function createPost(Request $request)
    {

        $validated =  $request->validate([
            'body' => 'required',
            'image' => 'required',
        ]);

        $imagePath = $request->file('image')->store('images', 'public');

        $post = $request->user()->posts()->create([
            'body' => $validated['body'],
            'image_path' => $imagePath,
        ]);
        $postWithUser = $post->with('user')->find($post->id);
        event(new PostCreated($postWithUser));
        return response()->json(['post' => $postWithUser]);
    }


    public function getPosts()
    {
        // get all posts but add is_liked_by_user to each post by using map
        $posts = Post::with('user')->latest()->get()->map(function ($post) {
            $post['is_liked_by_user'] = $post->isLikedByUser(Auth::user());
            return $post;
        });
        return response()->json(['posts' => PostResource::collection($posts)]);
    }
}

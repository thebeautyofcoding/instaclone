<?php

namespace App\Http\Controllers;

use App\Events\PostLiked;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Post $post)
    {
        // prevent user from liking their own post
        if ($post->user_id === auth()->id()) {
            return response()->json(['message' => 'You cannot like your own post'], 403);
        }

        // check if user has already liked the post, if so delete it
        if ($post->isLikedByUser(auth()->id())) {
            $post->likes()->where('user_id', auth()->id())->delete();
            $post->load('user'); // load user relationship
            $post->is_liked_by_user = false;
            event(new PostLiked($post));
            return response()->json([
                'message' => 'Post unliked',
                'is_liked_by_user' => false
            ]);
        }
        // if not, create a new like
        $post->likes()->create([
            'user_id' => auth()->id(),
        ]);
        $post->load('user'); // load user relationship
        $post->is_liked_by_user = true;
        event(new PostLiked($post));
        return response()->json([
            'message' => 'Post liked',
            'is_liked_by_user' => true
        ], 201);
    }
}

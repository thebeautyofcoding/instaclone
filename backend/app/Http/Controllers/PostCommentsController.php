<?php

namespace App\Http\Controllers;

use App\Events\CommentPosted;
use App\Models\Post;
use Illuminate\Http\Request;

class PostCommentsController extends Controller
{

    public function getCommentsWithReplies(Request $request, Post $post)
    {

        return response()->json(['comments' => $post->comments()->with(['user', 'replies', 'replies.user'])->get()]);
    }


    public function storeComments(Request $request, Post $post)
    {

        $validated = $request->validate([
            'body' => 'required|max:1000',
        ]);

        $comment = $post->comments()->create([
            'body' => $validated['body'],
            'user_id' => auth()->id(),
        ]);

        $comment->load('user');
        event(new CommentPosted($comment));

        return response()->json(['comment' => $comment], 201);
    }
}

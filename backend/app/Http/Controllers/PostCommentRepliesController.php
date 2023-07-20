<?php

namespace App\Http\Controllers;

use App\Events\ReplyPosted;
use App\Models\Comment;
use Illuminate\Http\Request;

class PostCommentRepliesController extends Controller
{
    public function store(Request $request, Comment $comment)
    {

        $reply = new Comment($request->validate([
            'body' => 'required|max:1000',
        ]));
        $reply->user()->associate(auth()->user());
        $reply->parent()->associate($comment);
        $comment->post->comments()->save($reply);
        $reply->save();
        //$comment->post->comments()->save($reply);
        event(new ReplyPosted($reply));
        return response()->json(['reply' => $reply], 201);
    }
}

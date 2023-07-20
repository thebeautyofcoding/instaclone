<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'image_path' => $this->image_path,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->diffForHumans(),
            'user' => new UserResource($this->user),
            'likes_count' => $this->likes()->count(),
            'comments_count' => $this->comments()->count(),
            'is_liked_by_user' => $this->isLikedByUser(auth()->id()),
            'user_id_who_liked' => auth()->id(),


        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentReadModelResource extends JsonResource
{
    public static $wrap = 'comment';

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'body' => $this->body,
            'author' => [
                'username' => $this->author->username,
                'bio' => $this->author->bio,
                'image' => $this->author->image,
                'following' => $this->author->following,
            ]
        ];
    }
}

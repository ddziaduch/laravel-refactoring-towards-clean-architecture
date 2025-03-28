<?php

namespace App\Http\Resources;

use Clean\Application\ReadModel\CommentReadModel;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public static $wrap = 'comment';

    public function toArray($request): array
    {
        assert($this->resource instanceof CommentReadModel);

        return [
            'id' => $this->resource->id,
            'createdAt' => $this->resource->createdAt,
            'updatedAt' => $this->resource->updatedAt,
            'body' => $this->resource->body,
            'author' => [
                'username' => $this->resource->author->username,
                'bio' => $this->resource->author->bio,
                'image' => $this->resource->author->image,
                'following' => in_array(
                    auth()->id(),
                    $this->resource->author->followerIds
                ),
            ]
        ];
    }
}

<?php

namespace Clean\Adapter\In\Http;

use Clean\Application\ReadModel\ArticleReadModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ArticleReadModel $resource
 */
final class ArticleReadModelResource extends JsonResource
{
    public static $wrap = 'article';

    public function toArray($request): array
    {
        return [
            'slug' => $this->resource->slug,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'body' => $this->resource->body,
            'tagList' => $this->resource->tagList,
            'createdAt' => $this->resource->createdAt,
            'updatedAt' => $this->resource->updatedAt,
            'favoritesCount' => $this->resource->favoritesCount,
            'favorited' => $this->resource->favorited,
            'author' => [
                'username' => $this->resource->author->username,
                'bio' => $this->resource->author->bio,
                'image' => $this->resource->author->image,
                'following' => $this->resource->author->following,
            ],
        ];
    }
}

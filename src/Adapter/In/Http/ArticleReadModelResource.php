<?php

namespace Clean\Adapter\In\Http;

use App\Models\User;
use Clean\Application\ReadModel\ArticleReadModel;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ArticleReadModel $resource
 */
final class ArticleReadModelResource extends JsonResource
{
    public static $wrap = 'article';

    public function __construct(
        ArticleReadModel $resource,
        private readonly Guard $guard,
    ) {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        $user = $this->guard->user();
        assert($user instanceof User || $user === null);

        return [
            'slug' => $this->resource->slug,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'body' => $this->resource->body,
            'tagList' => $this->resource->tagList,
            'createdAt' => $this->resource->createdAt,
            'updatedAt' => $this->resource->updatedAt,
            'favoritesCount' => $this->resource->favoritesCount,
            'favorited' => $user !== null
                ? $user->favoritedArticles->contains($this->resource->id)
                : false,
            'author' => [
                'username' => $this->resource->author->username,
                'bio' => $this->resource->author->bio,
                'image' => $this->resource->author->image,
                'following' => $user !== null
                    ? $user->following->contains($this->resource->author->id)
                    : false,
            ],
        ];
    }
}

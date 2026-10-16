<?php

namespace Conduit\Article\Adapter\Primary\Http;

use Conduit\Article\Application\ReadModel\ArticleReadModel;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ArticleReadModel $resource
 */
final class ArticleResource extends JsonResource
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
            'createdAt' => $this->formatDate($this->resource->createdAt),
            'updatedAt' => $this->formatDate($this->resource->updatedAt),
            'favorited' => $this->resource->favorited,
            'favoritesCount' => $this->resource->favoritesCount,
            'author' => [
                'username' => $this->resource->author->username,
                'bio' => $this->resource->author->bio,
                'image' => $this->resource->author->image,
                'following' => $this->resource->author->following,
            ],
        ];
    }

    private function formatDate(DateTimeImmutable $date): string
    {
        return $date->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z');
    }
}

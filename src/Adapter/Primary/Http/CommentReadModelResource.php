<?php

namespace Clean\Adapter\Primary\Http;

use Clean\Application\ReadModel\CommentReadModel;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property CommentReadModel $resource
 */
final class CommentReadModelResource extends JsonResource
{
    public static $wrap = 'comment';

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'createdAt' => $this->formatDate($this->resource->createdAt),
            'updatedAt' => $this->formatDate($this->resource->updatedAt),
            'body' => $this->resource->body,
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

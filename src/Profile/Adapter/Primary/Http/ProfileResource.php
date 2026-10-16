<?php

namespace Conduit\Profile\Adapter\Primary\Http;

use Conduit\Profile\Application\ReadModel\ProfileReadModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ProfileReadModel $resource
 */
final class ProfileResource extends JsonResource
{
    public static $wrap = 'profile';

    public function toArray($request): array
    {
        return [
            'username' => $this->resource->username,
            'bio' => $this->resource->bio,
            'image' => $this->resource->image,
            'following' => $this->resource->following,
        ];
    }
}

<?php

namespace Conduit\Identity\Adapter\Primary\Http;

use Conduit\Identity\Application\ReadModel\AuthenticatedUser;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property AuthenticatedUser $resource
 */
final class UserResource extends JsonResource
{
    public static $wrap = 'user';

    public function toArray($request): array
    {
        return [
            'email' => $this->resource->user->email,
            'token' => $this->resource->token,
            'username' => $this->resource->user->username,
            'bio' => $this->resource->user->bio,
            'image' => $this->resource->user->image,
        ];
    }
}

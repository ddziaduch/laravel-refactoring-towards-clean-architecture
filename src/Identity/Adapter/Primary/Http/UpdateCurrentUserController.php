<?php

namespace Conduit\Identity\Adapter\Primary\Http;

use Conduit\Identity\Application\Port\Primary\UpdateUser;
use Conduit\Identity\Application\ReadModel\AuthenticatedUser;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

final readonly class UpdateCurrentUserController
{
    public function __construct(private UpdateUser $updateUser) {}

    public function __invoke(UpdateCurrentUserRequest $request, JWTAuth $jwt): UserResource
    {
        return new UserResource(new AuthenticatedUser(
            ($this->updateUser)($request->user()->getAuthIdentifier(), $request->changes()),
            (string) $jwt->getToken(),
        ));
    }
}

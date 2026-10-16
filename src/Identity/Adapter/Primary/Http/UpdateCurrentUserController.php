<?php

namespace Conduit\Identity\Adapter\Primary\Http;

use Conduit\Identity\Application\Port\Primary\UpdateUser;
use Conduit\Identity\Application\ReadModel\AuthenticatedUser;
use Illuminate\Contracts\Auth\Guard;
use Tymon\JWTAuth\JWTAuth;

final readonly class UpdateCurrentUserController
{
    public function __construct(
        private UpdateUser $updateUser,
        private Guard $guard,
        private JWTAuth $jwt,
    ) {}

    public function __invoke(UpdateCurrentUserRequest $request): UserResource
    {
        return new UserResource(new AuthenticatedUser(
            ($this->updateUser)($this->guard->id(), $request->changes()),
            (string) $this->jwt->getToken(),
        ));
    }
}

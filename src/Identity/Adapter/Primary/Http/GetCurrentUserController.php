<?php

namespace Conduit\Identity\Adapter\Primary\Http;

use Conduit\Identity\Application\Port\Primary\GetUser;
use Conduit\Identity\Application\ReadModel\AuthenticatedUser;
use Illuminate\Contracts\Auth\Guard;
use Tymon\JWTAuth\JWTAuth;

final readonly class GetCurrentUserController
{
    public function __construct(
        private GetUser $getUser,
        private Guard $guard,
        private JWTAuth $jwt,
    ) {}

    public function __invoke(): UserResource
    {
        return new UserResource(new AuthenticatedUser(
            ($this->getUser)($this->guard->id()),
            (string) $this->jwt->getToken(),
        ));
    }
}

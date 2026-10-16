<?php

namespace Conduit\Identity\Adapter\Primary\Http;

use Conduit\Identity\Application\Port\Primary\GetUser;
use Conduit\Identity\Application\ReadModel\AuthenticatedUser;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

final readonly class GetCurrentUserController
{
    public function __construct(private GetUser $getUser) {}

    public function __invoke(Request $request, JWTAuth $jwt): UserResource
    {
        return new UserResource(new AuthenticatedUser(
            ($this->getUser)($request->user()->getAuthIdentifier()),
            (string) $jwt->getToken(),
        ));
    }
}

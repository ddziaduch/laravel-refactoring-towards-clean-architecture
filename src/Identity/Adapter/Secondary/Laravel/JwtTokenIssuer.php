<?php

namespace Conduit\Identity\Adapter\Secondary\Laravel;

use Conduit\Identity\Application\Port\Secondary\TokenIssuer;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Tymon\JWTAuth\JWTGuard;

final readonly class JwtTokenIssuer implements TokenIssuer
{
    public function __construct(private AuthFactory $auth) {}

    public function issueFor(int $userId): string
    {
        /** @var JWTGuard $guard */
        $guard = $this->auth->guard('api');

        return $guard->tokenById($userId);
    }
}

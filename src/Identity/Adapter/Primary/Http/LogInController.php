<?php

namespace Conduit\Identity\Adapter\Primary\Http;

use App\Exceptions\ConduitException;
use Conduit\Identity\Application\Exception\InvalidCredentials;
use Conduit\Identity\Application\Port\Primary\LogIn;
use Symfony\Component\HttpFoundation\Response;

final readonly class LogInController
{
    public function __construct(private LogIn $logIn) {}

    public function __invoke(LogInRequest $request): UserResource
    {
        try {
            $user = ($this->logIn)($request->validated('user.email'), $request->validated('user.password'));
        } catch (InvalidCredentials) {
            throw new ConduitException(['credentials' => ['invalid']], Response::HTTP_UNAUTHORIZED);
        }

        return new UserResource($user);
    }
}

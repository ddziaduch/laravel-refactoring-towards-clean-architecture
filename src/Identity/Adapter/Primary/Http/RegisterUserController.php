<?php

namespace Conduit\Identity\Adapter\Primary\Http;

use Conduit\Identity\Application\Port\Primary\RegisterUser;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class RegisterUserController
{
    public function __construct(private RegisterUser $registerUser) {}

    public function __invoke(RegisterUserRequest $request): JsonResponse
    {
        $user = ($this->registerUser)(
            $request->validated('user.username'),
            $request->validated('user.email'),
            $request->validated('user.password'),
        );

        return (new UserResource($user))->response()->setStatusCode(Response::HTTP_CREATED);
    }
}

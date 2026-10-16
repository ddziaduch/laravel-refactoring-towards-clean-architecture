<?php

namespace Conduit\Identity\Application\UseCase;

use Conduit\Identity\Application\Exception\UserNotFound;
use Conduit\Identity\Application\Port\Primary\GetUser;
use Conduit\Identity\Application\Port\Secondary\UserRepository;
use Conduit\Identity\Application\ReadModel\UserReadModel;

final readonly class GetUserUseCase implements GetUser
{
    public function __construct(private UserRepository $users) {}

    public function __invoke(int $userId): UserReadModel
    {
        $user = $this->users->find($userId) ?? throw UserNotFound::withId($userId);

        return UserReadModel::fromUser($user);
    }
}

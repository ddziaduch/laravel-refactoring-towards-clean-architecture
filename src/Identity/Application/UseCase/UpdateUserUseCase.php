<?php

namespace Conduit\Identity\Application\UseCase;

use Conduit\Identity\Application\Exception\UserNotFound;
use Conduit\Identity\Application\Port\Primary\UpdateUser;
use Conduit\Identity\Application\Port\Primary\UserChanges;
use Conduit\Identity\Application\Port\Secondary\PasswordHasher;
use Conduit\Identity\Application\Port\Secondary\UserRepository;
use Conduit\Identity\Application\ReadModel\UserReadModel;
use Conduit\Identity\Domain\Email;
use Conduit\Identity\Domain\Username;

final readonly class UpdateUserUseCase implements UpdateUser
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $passwordHasher,
    ) {}

    public function __invoke(int $userId, UserChanges $changes): UserReadModel
    {
        $user = $this->users->find($userId) ?? throw UserNotFound::withId($userId);

        if ($changes->has('username')) {
            $user = $user->rename(new Username($changes->get('username')));
        }

        if ($changes->has('email')) {
            $user = $user->changeEmail(new Email($changes->get('email')));
        }

        if ($changes->has('password')) {
            $user = $user->changePassword($this->passwordHasher->hash($changes->get('password')));
        }

        if ($changes->has('bio')) {
            $user = $user->changeBio($changes->get('bio'));
        }

        if ($changes->has('image')) {
            $user = $user->changeImage($changes->get('image'));
        }

        $this->users->save($user);

        return UserReadModel::fromUser($user);
    }
}

<?php

namespace Conduit\Identity\Application\UseCase;

use Conduit\Identity\Application\Port\Primary\RegisterUser;
use Conduit\Identity\Application\Port\Secondary\PasswordHasher;
use Conduit\Identity\Application\Port\Secondary\TokenIssuer;
use Conduit\Identity\Application\Port\Secondary\UserRepository;
use Conduit\Identity\Application\ReadModel\AuthenticatedUser;
use Conduit\Identity\Application\ReadModel\UserReadModel;
use Conduit\Identity\Domain\Email;
use Conduit\Identity\Domain\User;
use Conduit\Identity\Domain\Username;

final readonly class RegisterUserUseCase implements RegisterUser
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $passwordHasher,
        private TokenIssuer $tokenIssuer,
    ) {}

    public function __invoke(string $username, string $email, string $password): AuthenticatedUser
    {
        $user = User::register(
            new Username($username),
            new Email($email),
            $this->passwordHasher->hash($password),
        );

        $id = $this->users->add($user);

        return new AuthenticatedUser(
            UserReadModel::fromUser($this->users->find($id)),
            $this->tokenIssuer->issueFor($id),
        );
    }
}

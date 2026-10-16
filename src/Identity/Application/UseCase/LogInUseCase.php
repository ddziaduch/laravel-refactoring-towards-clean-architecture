<?php

namespace Conduit\Identity\Application\UseCase;

use Conduit\Identity\Application\Exception\InvalidCredentials;
use Conduit\Identity\Application\Port\Primary\LogIn;
use Conduit\Identity\Application\Port\Secondary\PasswordHasher;
use Conduit\Identity\Application\Port\Secondary\TokenIssuer;
use Conduit\Identity\Application\Port\Secondary\UserRepository;
use Conduit\Identity\Application\ReadModel\AuthenticatedUser;
use Conduit\Identity\Application\ReadModel\UserReadModel;

final readonly class LogInUseCase implements LogIn
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $passwordHasher,
        private TokenIssuer $tokenIssuer,
    ) {}

    public function __invoke(string $email, string $password): AuthenticatedUser
    {
        $user = $this->users->findByEmail($email);

        if ($user === null || ! $this->passwordHasher->verify($password, $user->password)) {
            throw new InvalidCredentials;
        }

        return new AuthenticatedUser(
            UserReadModel::fromUser($user),
            $this->tokenIssuer->issueFor($user->id),
        );
    }
}

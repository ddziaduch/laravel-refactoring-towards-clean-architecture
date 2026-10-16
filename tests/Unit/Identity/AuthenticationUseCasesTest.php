<?php

namespace Tests\Unit\Identity;

use Conduit\Identity\Application\Exception\InvalidCredentials;
use Conduit\Identity\Application\Port\Secondary\PasswordHasher;
use Conduit\Identity\Application\Port\Secondary\TokenIssuer;
use Conduit\Identity\Application\Port\Secondary\UserRepository;
use Conduit\Identity\Application\UseCase\LogInUseCase;
use Conduit\Identity\Application\UseCase\RegisterUserUseCase;
use Conduit\Identity\Domain\Exception\InvalidEmail;
use Conduit\Identity\Domain\HashedPassword;
use Conduit\Identity\Domain\User;
use PHPUnit\Framework\TestCase;

class AuthenticationUseCasesTest extends TestCase
{
    private InMemoryUserRepository $users;

    protected function setUp(): void
    {
        $this->users = new InMemoryUserRepository;
    }

    public function test_registration_stores_a_hashed_password_and_issues_a_token(): void
    {
        $registered = $this->register('john', 'john@example.com', 'secret-password');

        $this->assertSame('john', $registered->user->username);
        $this->assertSame('token-for-1', $registered->token);
        $this->assertSame('hashed:secret-password', $this->users->find(1)->password->value);
    }

    public function test_registration_rejects_an_invalid_email(): void
    {
        $this->expectException(InvalidEmail::class);

        $this->register('john', 'not-an-email', 'secret-password');
    }

    public function test_login_issues_a_token_for_valid_credentials(): void
    {
        $this->register('john', 'john@example.com', 'secret-password');

        $loggedIn = $this->logIn()('john@example.com', 'secret-password');

        $this->assertSame('john', $loggedIn->user->username);
        $this->assertSame('token-for-1', $loggedIn->token);
    }

    public function test_login_rejects_a_wrong_password_and_an_unknown_email(): void
    {
        $this->register('john', 'john@example.com', 'secret-password');

        foreach ([['john@example.com', 'wrong'], ['nobody@example.com', 'secret-password']] as [$email, $password]) {
            try {
                $this->logIn()($email, $password);
                $this->fail('Expected invalid credentials.');
            } catch (InvalidCredentials) {
                $this->addToAssertionCount(1);
            }
        }
    }

    private function register(string $username, string $email, string $password): mixed
    {
        return (new RegisterUserUseCase($this->users, new FakePasswordHasher, new FakeTokenIssuer))(
            $username,
            $email,
            $password,
        );
    }

    private function logIn(): LogInUseCase
    {
        return new LogInUseCase($this->users, new FakePasswordHasher, new FakeTokenIssuer);
    }
}

final class InMemoryUserRepository implements UserRepository
{
    /**
     * @var array<int, User>
     */
    private array $users = [];

    public function add(User $user): int
    {
        $id = count($this->users) + 1;
        $this->users[$id] = new User($id, $user->username, $user->email, $user->password, $user->bio, $user->image);

        return $id;
    }

    public function save(User $user): void
    {
        $this->users[$user->id] = $user;
    }

    public function find(int $id): ?User
    {
        return $this->users[$id] ?? null;
    }

    public function findByEmail(string $email): ?User
    {
        return array_values(array_filter($this->users, fn (User $user): bool => $user->email->value === $email))[0] ?? null;
    }

    public function findByUsername(string $username): ?User
    {
        return array_values(array_filter($this->users, fn (User $user): bool => $user->username->value === $username))[0] ?? null;
    }

    public function findMany(array $ids): array
    {
        return array_values(array_intersect_key($this->users, array_flip($ids)));
    }
}

final class FakePasswordHasher implements PasswordHasher
{
    public function hash(string $plainPassword): HashedPassword
    {
        return new HashedPassword('hashed:'.$plainPassword);
    }

    public function verify(string $plainPassword, HashedPassword $hashedPassword): bool
    {
        return $hashedPassword->value === 'hashed:'.$plainPassword;
    }
}

final class FakeTokenIssuer implements TokenIssuer
{
    public function issueFor(int $userId): string
    {
        return 'token-for-'.$userId;
    }
}

<?php

namespace Tests\Unit\Profile;

use Conduit\Profile\Application\Exception\ProfileNotFound;
use Conduit\Profile\Application\Port\Secondary\FollowRepository;
use Conduit\Profile\Application\Port\Secondary\UserAccount;
use Conduit\Profile\Application\Port\Secondary\UserAccounts;
use Conduit\Profile\Application\Service\ProfileFinder;
use Conduit\Profile\Application\UseCase\FollowUserUseCase;
use Conduit\Profile\Application\UseCase\UnfollowUserUseCase;
use Conduit\Profile\Domain\Follow;
use PHPUnit\Framework\TestCase;

/**
 * The Profile module is tested without the Identity module: its own
 * UserAccounts port is replaced by an in-memory implementation.
 */
class FollowUserUseCaseTest extends TestCase
{
    private InMemoryFollowRepository $follows;

    private ProfileFinder $profiles;

    protected function setUp(): void
    {
        $this->follows = new InMemoryFollowRepository;
        $this->profiles = new ProfileFinder(
            new InMemoryUserAccounts([new UserAccount(1, 'alice', null, null), new UserAccount(2, 'bob', 'Bio', null)]),
            $this->follows,
        );
    }

    public function test_following_is_idempotent_and_reflected_in_the_profile(): void
    {
        $follow = new FollowUserUseCase($this->profiles, $this->follows);

        $follow(1, 'bob');
        $profile = $follow(1, 'bob');

        $this->assertTrue($profile->following);
        $this->assertSame('Bio', $profile->bio);
        $this->assertSame([2], $this->follows->followedBy(1));
    }

    public function test_unfollowing_removes_the_follow(): void
    {
        (new FollowUserUseCase($this->profiles, $this->follows))(1, 'bob');

        $profile = (new UnfollowUserUseCase($this->profiles, $this->follows))(1, 'bob');

        $this->assertFalse($profile->following);
        $this->assertSame([], $this->follows->followedBy(1));
    }

    public function test_following_an_unknown_user_fails(): void
    {
        $this->expectException(ProfileNotFound::class);

        (new FollowUserUseCase($this->profiles, $this->follows))(1, 'nobody');
    }
}

final readonly class InMemoryUserAccounts implements UserAccounts
{
    /**
     * @param  list<UserAccount>  $accounts
     */
    public function __construct(private array $accounts) {}

    public function findByUsername(string $username): ?UserAccount
    {
        return array_values(array_filter($this->accounts, fn (UserAccount $account): bool => $account->username === $username))[0] ?? null;
    }

    public function findByIds(array $ids): array
    {
        $found = [];

        foreach ($this->accounts as $account) {
            if (in_array($account->id, $ids, true)) {
                $found[$account->id] = $account;
            }
        }

        return $found;
    }
}

final class InMemoryFollowRepository implements FollowRepository
{
    /**
     * @var array<string, Follow>
     */
    private array $follows = [];

    public function add(Follow $follow): void
    {
        $this->follows[$follow->followerId.'-'.$follow->followedId] = $follow;
    }

    public function remove(Follow $follow): void
    {
        unset($this->follows[$follow->followerId.'-'.$follow->followedId]);
    }

    public function followedAmong(int $followerId, array $candidateIds): array
    {
        return array_values(array_intersect($this->followedBy($followerId), $candidateIds));
    }

    public function followedBy(int $followerId): array
    {
        return array_values(array_map(
            fn (Follow $follow): int => $follow->followedId,
            array_filter($this->follows, fn (Follow $follow): bool => $follow->followerId === $followerId),
        ));
    }
}

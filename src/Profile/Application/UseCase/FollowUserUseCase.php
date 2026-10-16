<?php

namespace Conduit\Profile\Application\UseCase;

use Conduit\Profile\Application\Port\Primary\FollowUser;
use Conduit\Profile\Application\Port\Secondary\FollowRepository;
use Conduit\Profile\Application\ReadModel\ProfileReadModel;
use Conduit\Profile\Application\Service\ProfileFinder;
use Conduit\Profile\Domain\Follow;

final readonly class FollowUserUseCase implements FollowUser
{
    public function __construct(
        private ProfileFinder $profiles,
        private FollowRepository $follows,
    ) {}

    public function __invoke(int $followerId, string $username): ProfileReadModel
    {
        $followed = $this->profiles->accountOf($username);

        $this->follows->add(new Follow($followerId, $followed->id));

        return $this->profiles->profileOf($followed, $followerId);
    }
}

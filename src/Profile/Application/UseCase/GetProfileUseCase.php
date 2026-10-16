<?php

namespace Conduit\Profile\Application\UseCase;

use Conduit\Profile\Application\Port\Primary\GetProfile;
use Conduit\Profile\Application\ReadModel\ProfileReadModel;
use Conduit\Profile\Application\Service\ProfileFinder;

final readonly class GetProfileUseCase implements GetProfile
{
    public function __construct(private ProfileFinder $profiles) {}

    public function __invoke(string $username, ?int $viewerId): ProfileReadModel
    {
        return $this->profiles->profileOf($this->profiles->accountOf($username), $viewerId);
    }
}

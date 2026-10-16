<?php

namespace Conduit\Profile\Adapter\Laravel;

use Conduit\Profile\Adapter\Secondary\Database\DatabaseFollowRepository;
use Conduit\Profile\Adapter\Secondary\Identity\IdentityUserAccounts;
use Conduit\Profile\Application\Port\Primary\FollowUser;
use Conduit\Profile\Application\Port\Primary\GetProfile;
use Conduit\Profile\Application\Port\Primary\UnfollowUser;
use Conduit\Profile\Application\Port\Secondary\FollowRepository;
use Conduit\Profile\Application\Port\Secondary\UserAccounts;
use Conduit\Profile\Application\PublicApi\ProfilesService;
use Conduit\Profile\Application\UseCase\FollowUserUseCase;
use Conduit\Profile\Application\UseCase\GetProfileUseCase;
use Conduit\Profile\Application\UseCase\UnfollowUserUseCase;
use Conduit\Profile\PublicApi\FollowGraph;
use Conduit\Profile\PublicApi\Profiles;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class ProfileServiceProvider extends ServiceProvider
{
    public array $bindings = [
        // Public API
        Profiles::class => ProfilesService::class,
        FollowGraph::class => ProfilesService::class,

        // Primary ports
        GetProfile::class => GetProfileUseCase::class,
        FollowUser::class => FollowUserUseCase::class,
        UnfollowUser::class => UnfollowUserUseCase::class,

        // Secondary ports
        FollowRepository::class => DatabaseFollowRepository::class,
        UserAccounts::class => IdentityUserAccounts::class,
    ];

    public function boot(): void
    {
        Route::middleware('api')->prefix('api')->group(__DIR__.'/routes.php');
    }
}

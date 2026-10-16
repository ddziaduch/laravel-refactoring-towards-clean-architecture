<?php

namespace Conduit\Identity\Adapter\Laravel;

use Conduit\Identity\Adapter\Secondary\Eloquent\EloquentUserRepository;
use Conduit\Identity\Adapter\Secondary\Laravel\JwtTokenIssuer;
use Conduit\Identity\Adapter\Secondary\Laravel\LaravelPasswordHasher;
use Conduit\Identity\Application\Port\Primary\GetUser;
use Conduit\Identity\Application\Port\Primary\LogIn;
use Conduit\Identity\Application\Port\Primary\RegisterUser;
use Conduit\Identity\Application\Port\Primary\UpdateUser;
use Conduit\Identity\Application\Port\Secondary\PasswordHasher;
use Conduit\Identity\Application\Port\Secondary\TokenIssuer;
use Conduit\Identity\Application\Port\Secondary\UserRepository;
use Conduit\Identity\Application\PublicApi\UserDirectoryService;
use Conduit\Identity\Application\UseCase\GetUserUseCase;
use Conduit\Identity\Application\UseCase\LogInUseCase;
use Conduit\Identity\Application\UseCase\RegisterUserUseCase;
use Conduit\Identity\Application\UseCase\UpdateUserUseCase;
use Conduit\Identity\PublicApi\UserDirectory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class IdentityServiceProvider extends ServiceProvider
{
    public array $bindings = [
        // Public API
        UserDirectory::class => UserDirectoryService::class,

        // Primary ports
        RegisterUser::class => RegisterUserUseCase::class,
        LogIn::class => LogInUseCase::class,
        GetUser::class => GetUserUseCase::class,
        UpdateUser::class => UpdateUserUseCase::class,

        // Secondary ports
        UserRepository::class => EloquentUserRepository::class,
        PasswordHasher::class => LaravelPasswordHasher::class,
        TokenIssuer::class => JwtTokenIssuer::class,
    ];

    public function boot(): void
    {
        Route::middleware('api')->prefix('api')->group(__DIR__.'/routes.php');
    }
}

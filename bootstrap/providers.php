<?php

use App\Providers\AppServiceProvider;
use Conduit\Article\Adapter\Laravel\ArticleServiceProvider;
use Conduit\Engagement\Adapter\Laravel\EngagementServiceProvider;
use Conduit\Identity\Adapter\Laravel\IdentityServiceProvider;
use Conduit\Profile\Adapter\Laravel\ProfileServiceProvider;

return [
    AppServiceProvider::class,
    IdentityServiceProvider::class,
    ProfileServiceProvider::class,
    ArticleServiceProvider::class,
    EngagementServiceProvider::class,
];

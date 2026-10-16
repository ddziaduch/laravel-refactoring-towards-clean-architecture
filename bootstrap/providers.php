<?php

use App\Providers\AppServiceProvider;
use Conduit\Identity\Adapter\Laravel\IdentityServiceProvider;

return [
    AppServiceProvider::class,
    IdentityServiceProvider::class,
];

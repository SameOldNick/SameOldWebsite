<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\BroadcastServiceProvider;
use App\Providers\ComponentServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\MenuServiceProvider;
use App\Providers\RouteServiceProvider;
use App\Providers\ValidationServiceProvider;
use App\Providers\ViewServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    BroadcastServiceProvider::class,
    EventServiceProvider::class,
    RouteServiceProvider::class,
    ComponentServiceProvider::class,
    ValidationServiceProvider::class,
    ViewServiceProvider::class,
    MenuServiceProvider::class,
];

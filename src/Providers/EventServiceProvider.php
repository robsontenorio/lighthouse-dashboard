<?php

namespace LighthouseDashboard\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Nuwave\Lighthouse\Events\ManipulateResult;
use LighthouseDashboard\Listeners\ManipulateResultListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ManipulateResult::class => [
            ManipulateResultListener::class,
        ]
    ];

    public function boot()
    {
        parent::boot();
    }
}

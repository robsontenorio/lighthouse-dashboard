<?php

namespace LighthouseDashboard\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use LighthouseDashboard\Listeners\QueryExecutedListener;
use Nuwave\Lighthouse\Events\ManipulateResult;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ManipulateResult::class => [
            QueryExecutedListener::class,
        ]
    ];

    public function boot()
    {
        parent::boot();
    }
}

<?php

namespace LighthouseDashboard\Facades;

use Illuminate\Support\Facades\Facade;

class LighthouseDashboard extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lighthouse-dashboard';
    }
}

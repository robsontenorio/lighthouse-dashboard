<?php

namespace LighthouseDashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use LighthouseDashboard\Models\Operation;
use LighthouseDashboard\Models\Schema;
use LighthouseDashboard\Traits\ParsesRangeFilter;

class WelcomeController
{
    public function index(Request $request)
    {
        $schema = Schema::first();


        return inertia('Welcome', [
            'schema' => $schema,
        ]);
    }
}

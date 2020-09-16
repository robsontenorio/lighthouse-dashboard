<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Operation;
use App\Models\Schema;
use App\Traits\ParsesRangeFilter;

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

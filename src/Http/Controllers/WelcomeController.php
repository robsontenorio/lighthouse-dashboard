<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;
use App\Models\Schema;
use App\Traits\ParsesRangeFilter;

class WelcomeController
{
    use ParsesRangeFilter;

    public function index(HttpRequest $request)
    {
        $schema = Schema::first();
        $range = $this->parseRange($request, 'last month');
        $requests_series = Request::seriesIn($range);
        $client_series = Client::seriesIn($range);

        return inertia('Welcome', [
            'schema' => $schema,
            'requests_series' => $requests_series,
            'client_series' => $client_series
        ]);
    }
}

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
        $range = $this->parseRange($request);

        $schema = Schema::first();
        $requests_series = Request::seriesIn($range);
        $client_series = Client::seriesIn($range);

        return inertia('Welcome', [
            'schema' => $schema,
            'requests_series' => $requests_series,
            'client_series' => $client_series,
            'start_date' => $request->input('start_date', 'last month'),
            'range' => $request->input('range')
        ]);
    }
}

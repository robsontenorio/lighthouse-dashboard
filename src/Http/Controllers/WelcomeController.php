<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Request;
use App\Models\Schema;
use App\Traits\ParsesRangeFilter;
use Illuminate\Http\Request as HttpRequest;

class WelcomeController
{
    use ParsesRangeFilter;

    public function index(HttpRequest $request)
    {
        $schema = Schema::first();
        $clients = Client::orderBy('username')->get();

        $range = $this->parseRange($request);
        $selectedClients = $request->input('clients', $clients->pluck('id')->toArray());

        $requests_series = Request::seriesIn($range, $selectedClients);
        $client_series = Client::seriesIn($range, $selectedClients);

        return inertia('Welcome', [
            'schema' => $schema,
            'clients' => $clients,
            'requests_series' => $requests_series,
            'client_series' => $client_series,
            'start_date' => $request->input('start_date', 'last month'),
            'range' => $request->input('range'),
            'selectedClients' => $selectedClients
        ]);
    }
}

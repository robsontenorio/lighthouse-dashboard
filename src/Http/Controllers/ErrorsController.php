<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Error;
use App\Traits\ParsesRangeFilter;
use Illuminate\Http\Request;

class ErrorsController
{
    use ParsesRangeFilter;

    public function index(Request $request)
    {
        $clients = Client::orderBy('username')->get();

        $range = $this->parseRange($request);
        $selectedClients = $request->input('clients', $clients->pluck('id')->toArray());

        $errors = Error::latestIn($range, $selectedClients);

        return inertia('Errors', [
            'errors' => $errors,
            'clients' => $clients,
            'selectedClients' => $selectedClients,
            'start_date' => $request->input('start_date', 'last month'),
            'range' => $request->input('range')
        ]);
    }
}

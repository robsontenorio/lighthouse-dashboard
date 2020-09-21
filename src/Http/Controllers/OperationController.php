<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\Operation;
use App\Traits\ParsesRangeFilter;

class OperationController
{
    use ParsesRangeFilter;

    public function index(Request $request)
    {
        $clients = Client::orderBy('username')->get();

        $range = $this->parseRange($request);
        $selectedClients = $request->input('clients', $clients->pluck('id')->toArray());

        $topOperations = Operation::topIn($range, $selectedClients);
        $slowlestOperations = Operation::slowIn($range, $selectedClients);

        return inertia('Operations', [
            'topOperations' => $topOperations,
            'slowlestOperations' => $slowlestOperations,
            'start_date' => $request->input('start_date', 'last month'),
            'range' => $request->input('range'),
            'clients' => $clients,
            'selectedClients' => $selectedClients
        ]);
    }

    public function sumary(Operation $operation, Request $request)
    {
        $clients = Client::orderBy('username')->get();

        $range = $this->parseRange($request);
        $selectedClients = $request->input('clients', $clients->pluck('id')->toArray());

        return $operation->sumaryWithClients($operation, $range, $selectedClients);
    }
}

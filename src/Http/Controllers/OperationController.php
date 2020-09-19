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
        $range = $this->parseRange($request);

        $topOperations = Operation::top($range);
        $slowlestOperations = Operation::slow($range);

        return inertia('Operations', [
            'topOperations' => $topOperations,
            'slowlestOperations' => $slowlestOperations,
            'start_date' => $request->input('start_date', 'today'),
            'range' => $request->input('range')
        ]);
    }

    public function sumary(Operation $operation, Request $request)
    {
        $range = $this->parseRange($request);

        return $operation->sumary($operation, $range);
    }
}

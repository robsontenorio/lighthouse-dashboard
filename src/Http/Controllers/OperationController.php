<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operation;
use App\Traits\ParsesRangeFilter;

class OperationController
{
    use ParsesRangeFilter;

    public function index(Request $request)
    {
        $range = $this->parseRange($request);

        // $operations = $schema->operations()
        //     ->get()
        //     ->map(function (Operation $operation) use ($range) {
        //         $metrics = $operation->tracings()
        //             ->whereBetween('created_at', $range)
        //             ->selectRaw('DATE(created_at) as date, count(id) as total')
        //             ->groupByRaw('date')
        //             ->orderBy('date')
        //             ->get();

        //         $operation->metrics = [
        //             'totals' => $metrics->pluck('total'),
        //             'dates' => $metrics->pluck('date'),
        //         ];

        //         return $operation;
        //     });

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

        return $operation->sumary($range);
    }
}

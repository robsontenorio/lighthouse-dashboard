<?php

namespace LighthouseDashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use LighthouseDashboard\Models\Field;
use LighthouseDashboard\Models\Operation;
use LighthouseDashboard\Models\Schema;
use LighthouseDashboard\Traits\ParsesRangeFilter;

class OperationController
{
    use ParsesRangeFilter;

    public function index(Request $request)
    {
        $range = $this->parseRange($request);

        $schema = Schema::first();

        $operations = $schema->operations()
            ->get()
            ->map(function (Operation $operation) use ($range) {
                $metrics = $operation->tracings()
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DATE(created_at) as date, count(id) as total')
                    ->groupByRaw('date')
                    ->orderBy('date')
                    ->get();

                $operation->metrics = [
                    'totals' => $metrics->pluck('total'),
                    'dates' => $metrics->pluck('date'),
                ];

                return $operation;
            });

        $topOperations = Operation::orderByDesc('total_requests')->get();
        $slowlestOperations = Operation::orderByDesc('average_duration')->get()->map(function ($operation) {
            // from nanoseconds to miliseconds
            $operation->average_duration = floor($operation->average_duration / 1000000);
            $operation->latest_duration = floor($operation->latest_duration / 1000000);

            return $operation;
        });

        return inertia('Operations', [
            'operations' => $operations,
            'topOperations' => $topOperations,
            'slowlestOperations' => $slowlestOperations
        ]);
    }
}

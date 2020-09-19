<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Request as AppRequest;
use Illuminate\Http\Request;
use App\Models\Schema;
use App\Traits\ParsesRangeFilter;
use Carbon\CarbonPeriod;

class WelcomeController
{
    use ParsesRangeFilter;

    public function index(Request $request)
    {
        $schema = Schema::first();

        $range = $this->parseRange($request, 'last month');

        $requests_series = AppRequest::query()
            ->selectRaw('DATE(requested_at) as x, count(*) as y')
            ->whereNotNull('duration')
            ->whereBetween('requested_at', $range)
            ->groupBy('x')
            ->orderBy('x')
            ->get();

        $client_series = Client::query()
            ->withCount(['requests as total' => function ($query) use ($range) {
                $query->whereNotNull('duration')
                    ->whereBetween('requested_at', $range);
            }])
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return ['x' => $item->username, 'y' => $item['total']];
            });

        return inertia('Welcome', [
            'schema' => $schema,
            'requests_series' => $this->fillEmptyRanges($requests_series, $range['end_date']),
            'client_series' => $client_series
        ]);
    }

    private function fillEmptyRanges($requests_series, $end_date)
    {
        $period = CarbonPeriod::between($requests_series->first()->x, $end_date);
        $series = collect();

        foreach ($period as $date) {
            if ($item = $requests_series->firstWhere('x', $date->toDateString())) {
                $series->add($item);
                continue;
            }

            $series->add(['x' => $date->toDateString(), 'y' => null]);
        }

        return $series;
    }
}

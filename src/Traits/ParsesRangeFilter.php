<?php

namespace LighthouseDashboard\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;

trait ParsesRangeFilter
{
    public function parseRange(Request $request, $start_date = 'today')
    {
        $start_date = $request->input('start_date', $start_date);
        $end_date = 'today';
        $range = $request->input('range');

        if ($range && $start_date === 'in custom range') {
            $start_date = $range[0];
            $end_date = $range[1];
        }

        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();

        if ($start_date > $end_date) {
            $start_date_temp = $start_date;
            $start_date  = $end_date;
            $end_date = $start_date_temp;
        }

        return [
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
    }
}

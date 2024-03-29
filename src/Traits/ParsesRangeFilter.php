<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;

trait ParsesRangeFilter
{
    public function parseRange(Request $request, $start_date = 'last month')
    {
        $start_date = $request->input('start_date', $start_date);
        $end_date = 'today';
        $range = $request->input('range');

        if ($range && $start_date === 'in custom range') {
            $start_date = $range[0];
            $end_date = $range[1];
        }

        if (!$range && $start_date === 'in custom range') {
            $start_date = 'last month';
        }

        $start_date = Carbon::parse($start_date);
        $end_date = Carbon::parse($end_date);

        // Fix date order
        if ($start_date > $end_date) {
            $start_date_temp = $start_date;
            $start_date  = $end_date;
            $end_date = $start_date_temp;
        }

        // Assure we get entire day range
        $start_date = $start_date->startOfDay();
        $end_date = $end_date->endOfDay();

        return [
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
    }
}

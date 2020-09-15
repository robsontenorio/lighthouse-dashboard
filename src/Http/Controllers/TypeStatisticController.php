<?php

namespace LighthouseDashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use LighthouseDashboard\Models\Field;
use LighthouseDashboard\Models\Type;
use LighthouseDashboard\Traits\ParsesRangeFilter;

class TypeStatisticController
{
    use ParsesRangeFilter;

    public function index(Request $request)
    {
        $range = $this->parseRange($request);

        $fields = Field::withCount(['statistics' => function (Builder $query) use ($range) {
            return $query->whereBetween('requested_at', $range);
        }])
            ->get();

        $types = Type::all()
            ->map(function ($type) use ($fields) {
                $type->fields = $fields->where('type_id', $type->id)->values();
                return $type;
            });

        return inertia('Types')->with([
            'types' => $types,
            'start_date' => $request->input('start_date', 'today'),
            'range' => $request->input('range')
        ]);
    }
}

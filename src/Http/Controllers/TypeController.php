<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Field;
use App\Models\Type;
use App\Traits\ParsesRangeFilter;

class TypeController
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

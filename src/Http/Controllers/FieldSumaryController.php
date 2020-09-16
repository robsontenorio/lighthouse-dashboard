<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Field;
use App\Models\Operation;
use App\Traits\ParsesRangeFilter;

class FieldSumaryController
{
    use ParsesRangeFilter;

    public function index(Field $field, Request $request)
    {
        $range = $this->parseRange($request);

        $sumary = Operation::whereHas('statistics', function ($query) use ($field, $range) {
            $query->where('field_id', $field->id)->whereBetween('requested_at', $range);
        })
            ->withCount(['statistics' => function (Builder $query) use ($field, $range) {
                $query->where('field_id', $field->id)->whereBetween('requested_at', $range);
            }])
            ->get();

        return $sumary;
    }
}

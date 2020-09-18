<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Field;
use App\Models\Operation;
use App\Traits\ParsesRangeFilter;

class FieldController
{
    use ParsesRangeFilter;

    public function sumary(Field $field, Request $request)
    {
        $range = $this->parseRange($request);

        $clients = Client::all();

        $sumary = $clients->map(function ($client) use ($field, $range) {
            $client->metrics =  Operation::query()
                ->with('field')
                ->whereHas('requests', function ($query) use ($client, $field, $range) {
                    $query->where('client_id', $client->id)->where('field_id', $field->id)->whereBetween('requested_at', $range);
                })
                ->withCount(['requests as total_requests' => function (Builder $query) use ($client, $field, $range) {
                    $query->where('client_id', $client->id)->where('field_id', $field->id)->whereBetween('requested_at', $range);
                }])
                ->get();

            return $client;
        })
            ->reject(fn ($client) => count($client->metrics) == 0)
            ->values();



        return $sumary;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Client;
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
        $clients = Client::orderBy('username')->get();

        $range = $this->parseRange($request);
        $selectedClients = $request->input('clients', $clients->pluck('id')->toArray());

        $fields = Field::query()
            ->withCount(['requests as total_requests' => function (Builder $query) use ($range, $selectedClients) {
                return $query->forClients($selectedClients)->inRange($range);
            }])
            ->get();

        $types = Type::all()
            ->map(function ($type) use ($fields) {
                $type->fields = $fields->where('type_id', $type->id)->values();
                return $type;
            });

        return inertia('Types')->with([
            'types' => $types,
            'start_date' => $request->input('start_date', 'last month'),
            'range' => $request->input('range'),
            'clients' => $clients,
            'selectedClients' => $selectedClients
        ]);
    }
}

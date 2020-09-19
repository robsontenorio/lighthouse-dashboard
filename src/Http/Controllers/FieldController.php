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

        return $field->sumaryWithClients($range);
    }
}

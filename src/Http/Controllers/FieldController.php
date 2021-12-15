<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Traits\ParsesRangeFilter;
use Illuminate\Http\Request;

class FieldController
{
    use ParsesRangeFilter;

    public function sumary(Field $field, Request $request)
    {
        $range = $this->parseRange($request);

        return $field->sumaryWithClients($range);
    }
}

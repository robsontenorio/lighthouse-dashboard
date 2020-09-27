<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Field;
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

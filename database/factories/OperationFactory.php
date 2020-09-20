<?php

namespace Database\Factories;

use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Schema;

class OperationFactory extends Factory
{
    public function definition()
    {
        return [
            'field_id' => Field::factory()
        ];
    }
}

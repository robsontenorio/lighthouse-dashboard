<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Operation;
use App\Models\Schema;

class OperationFactory extends Factory
{
    // protected $model = Operation::class;

    public function definition()
    {
        return [
            'schema_id' => Schema::factory(),
            'name' => $this->faker->userName
        ];
    }
}

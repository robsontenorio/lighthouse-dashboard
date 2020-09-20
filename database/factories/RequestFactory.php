<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Field;
use App\Models\Operation;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestFactory extends Factory
{
    public function definition()
    {
        return [
            'field_id' => Field::factory(),
            'operation_id' => Operation::factory(),
            'client_id' => Client::factory(),
            'requested_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'duration' => $this->faker->randomNumber(8)
        ];
    }
}

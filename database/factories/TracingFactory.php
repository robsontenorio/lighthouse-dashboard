<?php

namespace Database\Factories;

use App\Models\Operation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TracingFactory extends Factory
{
    public function definition()
    {
        return [
            'operation_id' => Operation::factory(),
            'start_time' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_time' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'duration' => $this->faker->randomNumber(),
            'request' => $this->faker->sentence(),
            'execution' => $this->faker->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now')
        ];
    }
}

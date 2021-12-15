<?php

namespace Database\Factories;

use App\Models\Request;
use Illuminate\Database\Eloquent\Factories\Factory;

class TracingFactory extends Factory
{
    public function definition()
    {
        return [
            'request_id' => Request::factory(),
            'start_time' => $this->faker->dateTimeBetween('last month'),
            'end_time' => $this->faker->dateTimeBetween('last month'),
            'duration' => $this->faker->randomNumber(),
            'request' => $this->faker->sentence(),
            'execution' => $this->faker->sentence(),
            'requested_at' => $this->faker->dateTimeBetween('last month')
        ];
    }
}

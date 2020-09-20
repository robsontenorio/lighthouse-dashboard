<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Type;

class FieldFactory extends Factory
{
    public function definition()
    {
        return [
            'type_id' => Type::factory(),
            'name' => $this->faker->word . $this->faker->randomNumber(3),
            'description' => $this->faker->sentence,
            'type_def' => $this->faker->name
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Schema;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeFactory extends Factory
{
    public function definition()
    {
        return [
            'schema_id' => Schema::factory(),
            'name' => $this->faker->name,
            'description' => $this->faker->sentence
        ];
    }
}

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
            'name' => $this->faker->unique()->firstName,
            'description' => $this->faker->sentence
        ];
    }

    public function ofQueryType()
    {
        return $this->state([
            'name' => 'Query'
        ]);
    }
}

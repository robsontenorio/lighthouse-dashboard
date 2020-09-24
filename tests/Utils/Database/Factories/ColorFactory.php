<?php

namespace Tests\Utils\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Utils\Models\Color;

class ColorFactory extends Factory
{
    protected $model = Color::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name
        ];
    }
}

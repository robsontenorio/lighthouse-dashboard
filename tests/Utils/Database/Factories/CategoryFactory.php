<?php

namespace Tests\Utils\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Utils\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name
        ];
    }
}

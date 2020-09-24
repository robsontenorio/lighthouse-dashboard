<?php

namespace Tests\Utils\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Utils\Models\Color;
use Tests\Utils\Models\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'color_id' => ColorFactory::new(),
            'category_id' => CategoryFactory::new(),
            'name' => $this->faker->name
        ];
    }
}

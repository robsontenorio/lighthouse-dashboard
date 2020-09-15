<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use LighthouseDashboard\Models\Field;
use LighthouseDashboard\Models\Type;

$factory->define(Field::class, function (Faker $faker) {
    return [
        'type_id' => factory(Type::class),
        'name' => $faker->name,
        'description' => $faker->sentence,
        'type_def' => $faker->name
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Models\Field;
use App\Models\Type;

$factory->define(Field::class, function (Faker $faker) {
    return [
        'type_id' => factory(Type::class),
        'name' => $faker->name,
        'description' => $faker->sentence,
        'type_def' => $faker->name
    ];
});

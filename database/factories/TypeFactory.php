<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use LighthouseDashboard\Models\Schema;
use LighthouseDashboard\Models\Type;

$factory->define(Type::class, function (Faker $faker) {
    return [
        'schema_id' => factory(Schema::class),
        'name' => $faker->name,
        'description' => $faker->sentence
    ];
});

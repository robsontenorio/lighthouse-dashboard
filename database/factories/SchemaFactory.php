<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use LighthouseDashboard\Models\Schema;

$factory->define(Schema::class, function (Faker $faker) {
    return [
        'name' => $faker->name
    ];
});

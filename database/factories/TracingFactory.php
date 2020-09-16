<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Models\Operation;
use App\Models\Tracing;

$factory->define(Tracing::class, function (Faker $faker) {
    return [
        'operation_id' => factory(Operation::class),
        'request' => $faker->sentence(),
        'start_time' => $faker->dateTimeBetween('-1 month', 'now'),
        'end_time' => $faker->dateTime(),
        'duration' => $faker->randomNumber(),
        'execution' => $faker->sentence()
    ];
});

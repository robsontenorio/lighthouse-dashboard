<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use LighthouseDashboard\Models\Operation;
use LighthouseDashboard\Models\Schema;

$factory->define(Operation::class, function (Faker $faker) {
    return [
        'schema_id' => factory(Schema::class),
        'name' => $faker->userName
    ];
});

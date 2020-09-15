<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use LighthouseDashboard\Models\Field;
use LighthouseDashboard\Models\FieldsOperations;
use LighthouseDashboard\Models\Operation;

$factory->define(FieldsOperations::class, function (Faker $faker) {
    return [
        'field_id' => factory(Field::class),
        'operation_id' => factory(Operation::class),
        'requested_at' => $faker->dateTimeBetween('-1 month', 'now')
    ];
});

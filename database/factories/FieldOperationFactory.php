<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Models\Field;
use App\Models\FieldsOperations;
use App\Models\Operation;

$factory->define(FieldsOperations::class, function (Faker $faker) {
    return [
        'field_id' => factory(Field::class),
        'operation_id' => factory(Operation::class),
        'requested_at' => $faker->dateTimeBetween('-1 month', 'now')
    ];
});

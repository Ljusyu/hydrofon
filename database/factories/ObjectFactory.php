<?php

use Faker\Generator as Faker;

$factory->define(Hydrofon\Object::class, function (Faker $faker) {
    return [
        'name'        => $faker->city,
        'description' => $faker->sentence,
        'facility'    => $faker->boolean(25),
    ];
});

<?php
use Faker\Generator as Faker;

/**  **/
$factory->define(App\Models\Request\Request::class, function (Faker $faker) {
    return [
        'requested_by_id' => factory(App\Models\User\User::class)->create()->id,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'completed' => $faker->boolean
    ];
});

<?php
use Faker\Generator as Faker;

$factory->define(App\Models\Request\SafetyReport::class, function (Faker $faker) {
    return [
        'requested_by_id' => factory(App\Models\User\User::class)->create()->id,
        'reporter_id' => factory(App\Models\User\User::class)->create()->id,
        'description' => $faker->text
    ];
});
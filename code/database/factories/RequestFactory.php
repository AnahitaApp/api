<?php
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Request\Request::class, function (Faker $faker) {
    return [
        'requested_by_id' => factory(App\Models\User\User::class)->create()->id,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
    ];
});
$factory->define(App\Models\Request\RequestedItem::class, function (Faker $faker) {
    return [
        'request_id' => factory(App\Models\Request\Request::class)->create()->id,
        'name' => $faker->name,
    ];
});
$factory->define(App\Models\Request\SafetyReport::class, function (Faker $faker) {
    return [
        'request_id' => factory(App\Models\Request\Request::class)->create()->id,
        'reporter_id' => factory(App\Models\User\User::class)->create()->id,
        'description' => $faker->text
    ];
});

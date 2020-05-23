<?php
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Organization\Organization::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
$factory->define(App\Models\Organization\OrganizationManager::class, function (Faker $faker) {
    return [
        'user_id' => factory(App\Models\User\User::class)->create()->id,
        'organization_id' => factory(App\Models\Organization\Organization::class)->create()->id,
        'role_id' => \App\Models\Role::ORGANIZATION_ADMIN,
    ];
});
$factory->define(App\Models\Organization\Location::class, function (Faker $faker) {
    return [
        'organization_id' => factory(App\Models\Organization\Organization::class)->create()->id,
        'name' => $faker->name,
        'address_line_1' => $faker->address,
        'city' => $faker->city,
        'country' => $faker->country,
    ];
});
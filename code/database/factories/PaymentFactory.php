<?php
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Payment\PaymentMethod::class, function (Faker $faker) {
    return [
        'owner_id' => factory(\App\Models\User\User::class)->create()->id,
        'owner_type' => 'user',
        'payment_method_key' => $faker->text(20),
        'payment_method_type' => $faker->text(20),
    ];
});
$factory->define(App\Models\Payment\Payment::class, function (Faker $faker) {
    return [
        'payment_method_id' => factory(\App\Models\Payment\PaymentMethod::class)->create()->id,
        'amount' => $faker->randomFloat(),
    ];
});
$factory->define(\App\Models\Payment\LineItem::class, function (Faker $faker) {
    return [
        'payment_id' => factory(\App\Models\Payment\Payment::class)->create()->id,
        'item_type' => 'donation',
        'amount' => $faker->numberBetween(0, 100),
    ];
});
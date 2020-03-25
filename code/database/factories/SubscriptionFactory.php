<?php
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Subscription\MembershipPlan::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'duration' => \App\Models\Subscription\MembershipPlan::DURATION_YEAR,
    ];
});
$factory->define(\App\Models\Subscription\MembershipPlanRate::class, function (Faker $faker) {
    return [
        'membership_plan_id' => factory(\App\Models\Subscription\MembershipPlan::class)->create()->id,
        'cost' => 10.00,
        'active' => 0,
    ];
});
$factory->define(\App\Models\Subscription\Subscription::class, function (Faker $faker) {
    return [
        'membership_plan_rate_id' => factory(\App\Models\Subscription\MembershipPlanRate::class)->create()->id,
        'payment_method_id' => factory(\App\Models\Payment\PaymentMethod::class)->create()->id,
        'subscriber_id' => factory(\App\Models\User\User::class)->create()->id,
        'subscriber_type' => 'user',
    ];
});
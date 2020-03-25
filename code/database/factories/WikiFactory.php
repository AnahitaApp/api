<?php
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\App\Models\Wiki\Article::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'created_by_id' => factory(\App\Models\User\User::class)->create()->id,
    ];
});

$factory->define(\App\Models\Wiki\Iteration::class, function (Faker $faker) {
    return [
        'content' => $faker->text,
        'article_id' => factory(\App\Models\Wiki\Article::class)->create()->id,
        'created_by_id' => factory(\App\Models\User\User::class)->create()->id,
    ];
});

$factory->define(\App\Models\Wiki\ArticleVersion::class, function (Faker $faker) {
    return [
        'article_id' => factory(\App\Models\Wiki\Article::class)->create()->id,
        'iteration_id' => factory(\App\Models\Wiki\Iteration::class)->create()->id,
    ];
});
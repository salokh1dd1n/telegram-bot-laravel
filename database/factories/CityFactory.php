<?php

use Faker\Generator as Faker;
use Faker\Provider\en_AU\Address as Australia;
use Faker\Provider\ru_RU\Address as Russian;
use Faker\Provider\en_US\Address as USA;

$factory->define(App\Models\City::class, function (Faker $faker) {
    $faker->addProvider(new Russian($faker));

    return [
        'title_uz' => $faker->state,
        'title_ru' => $faker->city,
        'title_en' => $faker->state,
    ];
});

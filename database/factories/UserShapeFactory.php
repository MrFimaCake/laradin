<?php

use Faker\Generator as Faker;

$factory->define(App\UserCoordinates::class, function (Faker $faker) {
    return [
        
        'coordinates' => [
            [
                'lat' => (float)rand(-100, 100) / 10,
                'lng' => (float)rand(-100, 100) / 10,
            ],
            [
                'lat' => (float)rand(-100, 100) / 10,
                'lng' => (float)rand(-100, 100) / 10,
            ],
            [
                'lat' => (float)rand(-100, 100) / 10,
                'lng' => (float)rand(-100, 100) / 10,
            ],
        ],
    ];
});

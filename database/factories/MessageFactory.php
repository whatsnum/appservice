<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Message;
use Faker\Generator as Faker;

$factory->define(Message::class, function (Faker $faker) {
    return [
      // 'reply'           => $faker->randomElement([null, '']),
      'message'         => $faker->realText(),
      'delivered_at'    => $faker->randomElement([null, now()]),
      'read_at'         => $faker->randomElement([null, now()]),
      // 'deleted_by'      => $faker->randomElement([null, now()]),
      // 'deleted_at'      => $faker->randomElement([null, now()]),
    ];
});

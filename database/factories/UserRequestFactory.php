<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UserRequest;
use Faker\Generator as Faker;

$factory->define(UserRequest::class, function (Faker $faker) {
    return [
      'other_user_id'      => App\User::inRandomOrder()->first()->id,
      'status'             => $faker->randomElement(['pending', 'rejected', 'accepted']),
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Contact;
use Faker\Generator as Faker;

$factory->define(Contact::class, function (Faker $faker) {
    return [
      'other_user_id' => App\User::inRandomOrder()->first()->id,
      'type'          => $faker->randomElement(['block', 'friend']),
    ];
});

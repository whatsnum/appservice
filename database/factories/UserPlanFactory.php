<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UserPlan;
use Faker\Generator as Faker;

$factory->define(UserPlan::class, function (Faker $faker) {
    return [
      // 'user_id'             => factory(App\User::class),
      'plan_id'             => 1,
      'transaction_id'      => 0,
      'no_contact_use'      => 10,
      'start_plan_date'     => now(),
      'end_plan_date'       => $faker->date(),
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\InterestUser;
use Faker\Generator as Faker;

$factory->define(InterestUser::class, function (Faker $faker) {
    return [
      'interest_id'      => App\Interest::inRandomOrder()->first()->id,
      // function(array $interest_user){
      //   return App\Interest::whereDoesntHave('users', function($q) use($interest_user){
      //     print_r($interest_user);
      //     $q->where('user_id', $interest_user['user_id']);
      //   })->first()->id;
      // },
    ];
});

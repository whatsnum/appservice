<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Country;
use App\State;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
  $country = Country::inRandomOrder()->first();
  $state = $country->states()->inRandomOrder()->first();
    return [
      'name'                => $faker->userName,
      'email'               => $faker->unique()->safeEmail,
      'phone_code'          => $country->phonecode,//$faker->randomNumber(3),
      'phone'               => $faker->unique()->randomNumber(8),
      'age'                 => $faker->numberBetween(17, 70),
      'gender'              => $faker->randomElement(['male', 'female']),
      // 'other_user_gender'   => $faker->randomElement(['male', 'female', 'both']),
      'city'                => $faker->city,
      'state'               => $state->name,//$faker->state,
      'country'             => $country->name,//$faker->country,
      'lat'                 => $faker->latitude($min = -90, $max = 90),     // 77.147489
      'lng'                 => $faker->longitude($min = -180, $max = 180),  // 86.211205
      'profile_step'        => 100,
      'email_verified_at'   => now(),
      'password'            => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
      'remember_token'      => Str::random(10),
    ];
});

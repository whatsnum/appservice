<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      factory(User::class, 2000)->create()->each(function ($user){
        $user->plans()->save(factory(App\UserPlan::class)->make());
        $interests = App\Interest::inRandomOrder()->limit(4)->pluck('id');
        // factory(App\InterestUser::class, 4)->make();
        // $interests = factory(App\InterestUser::class, 4)->make()->toArray()
        $user->interests()->attach(
          $interests
        );
      });
    }
}

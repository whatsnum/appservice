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
      factory(User::class, 10000)->create()->each(function ($user){
        $user->plans()->save(factory(App\UserPlan::class)->make());
        $job_title = App\JobTitle::inRandomOrder()->first();
        $user->job_title()->create(['name' => 'job_title', 'value' => $job_title->name]);

        $user->requested()->createMany(factory(App\UserRequest::class, 10)->make()->toArray());

        $interests = App\Interest::inRandomOrder()->limit(4)->pluck('id');
        // factory(App\InterestUser::class, 4)->make();
        // $interests = factory(App\InterestUser::class, 4)->make()->toArray()
        $user->interests()->attach(
          $interests
        );
      });
    }
}

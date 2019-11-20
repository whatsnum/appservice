<?php

use Illuminate\Database\Seeder;
use App\UserPlan;

class UserPlanSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      factory(UserPlan::class, 2)->create()->each(function ($userPlan){
        $userPlan->save();
        // plan()->save(factory(App\Plan::class)->make());
      });
    }
}

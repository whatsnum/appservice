<?php

use Illuminate\Database\Seeder;
use App\User;
use Faker\Generator as Faker;

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
        $num_conv = 4;
        $num_conv_msg = 4;
        $user->plans()->save(factory(App\UserPlan::class)->make());
        $job_title = App\JobTitle::inRandomOrder()->first();
        $user->job_title()->create(['name' => 'job_title', 'value' => $job_title->name]);

        $user->requested()->createMany(factory(App\UserRequest::class, 10)->make()->toArray());

        $user->contacts()->createMany(factory(App\Contact::class, 10)->make()->toArray());

        $interests = App\Interest::inRandomOrder()->limit(4)->pluck('id');
        // factory(App\InterestUser::class, 4)->make();
        // $interests = factory(App\InterestUser::class, 4)->make()->toArray()
        $user->interests()->attach( $interests );

        // -check in conversations
        $conversations = $user->conversations()->get();
        // -create message foreach conversations
        $conversations->map(function($conversation) use($user, $num_conv_msg){
          $conversation->messages()->createMany(factory(App\Message::class, $num_conv_msg)->make(['user_id' => $user->id])->toArray());
        });

        $users = $user->inRandomOrder()->limit($num_conv)->get();

        // -create conversations 6 where does not exists with user
        $conversations = $user->converse()->createMany(factory(App\ConversationUser::class, $num_conv)->make()->toArray());
        // attach users
        foreach ($conversations as $key => $value) {
          if (isset($users[$key])) {
            $user->conversations()->attach($value);
            $users[$key]->conversations()->attach($value);
          }
        }
        // -create message foreach conversations
        $conversations->map(function($conversation) use($user, $num_conv_msg){
          $conversation->messages()->createMany(factory(App\Message::class, $num_conv_msg)->make(['user_id' => $user->id])->toArray());
        });
      });
    }
}

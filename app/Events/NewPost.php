<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;
use App\Activity;

class NewPost implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $activity, $country, $state, $gender, $age;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Activity $activity)
    {
      $activity->load(['activeable'], [ User::class, Post::class => ['author']]);
      if ($activity->activeable_type == Post::class) {
        $author = $activity->activeable->author;
      } else {
        $author = $activity->activeable;
      }

      $this->activity = $activity;
      $this->country = $author->country;
      $this->state = $author->state;
      $this->gender = $author->gender;
      $this->age = $author->age;
    }

    public function broadcastAs()
    {
      return 'new-posts';
    }

    private function spaceWithUnderscore($string){
      return $this->cleanString(implode("_", explode(" ", $string)));
    }

    // 'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A',
    // 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
    // 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O',
    // 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
    // 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
    // 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
    // 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o',
    // 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
    // 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y'

    private function cleanString($str){
      $unwanted_array = [
        'Š'=>'', 'š'=>'', 'Ž'=>'', 'ž'=>'', 'À'=>'', 'Á'=>'', 'Â'=>'', 'Ã'=>'', 'Ä'=>'', 'Å'=>'', 'Æ'=>'',
        'Ç'=>'', 'È'=>'', 'É'=>'',
        'Ê'=>'', 'Ë'=>'', 'Ì'=>'', 'Í'=>'', 'Î'=>'', 'Ï'=>'', 'Ñ'=>'', 'Ò'=>'', 'Ó'=>'', 'Ô'=>'', 'Õ'=>'',
        'Ö'=>'', 'Ø'=>'', 'Ù'=>'',
        'Ú'=>'', 'Û'=>'', 'Ü'=>'', 'Ý'=>'', 'Þ'=>'', 'ß'=>'s', 'à'=>'', 'á'=>'', 'â'=>'', 'ã'=>'', 'ä'=>'',
        'å'=>'', 'æ'=>'', 'ç'=>'',
        'è'=>'', 'é'=>'', 'ê'=>'', 'ë'=>'', 'ì'=>'', 'í'=>'', 'î'=>'', 'ï'=>'', 'ð'=>'', 'ñ'=>'', 'ò'=>'',
        'ó'=>'', 'ô'=>'', 'õ'=>'',
        'ö'=>'', 'ø'=>'', 'ù'=>'', 'ú'=>'', 'û'=>'', 'ý'=>'', 'þ'=>'', 'ÿ'=>'', '\'' => ''
      ];
      // Log::debug(strtr( $str, $unwanted_array ));
      return strtr( $str, $unwanted_array );
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {

      // return ["posts-id"];
      // posts-United_States-California-female
      return [
        // for single gender subscribers on location
        "posts-{$this->spaceWithUnderscore($this->country)}-{$this->spaceWithUnderscore($this->state)}-{$this->gender}",
        // for both gender subscribers on location
        "posts-{$this->spaceWithUnderscore($this->country)}-{$this->spaceWithUnderscore($this->state)}-both",
        //
        // "posts-{$this->spaceWithUnderscore($this->country)}-{$this->spaceWithUnderscore($this->state)}-{$this->gender}",
        // for single gender subscribers on all location
        "posts-{$this->gender}",
        // for both gender subscribers on all location
        "posts-both",
      ];
      // return new PrivateChannel("posts-id");
    }
}

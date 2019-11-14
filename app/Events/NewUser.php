<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewUser implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user, $images, $feedImages;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user)
    {
      $this->user = $user;
      $this->images = $user->images;
      $this->feedImages = $user->feedImages;
      // $this->request_detail = $user->request_detail;
    }

    public function broadcastAs()
    {
      return 'new-user';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
      $user = $this->user;
      // dd($this->user);
      return [
        // for single gender subscribers on country's state
        "user-".spaceWithUnderscore($user->country)."-".spaceWithUnderscore($user->state)."-".$user->gender,
        // for both gender subscribers on country's state
        "user-".spaceWithUnderscore($user->country)."-".spaceWithUnderscore($user->state)."-both",
        // for single gender subscribers on country
        "user-".spaceWithUnderscore($user->country)."-".$user->gender,
        // for both gender subscribers on country
        "user-".spaceWithUnderscore($user->country)."-both",
        // for single gender subscribers on all location
        "user-".$user->gender,
        // for both gender subscribers on all location
        "user-both",
      ];
    }
}

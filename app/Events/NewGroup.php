<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewGroup implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $group, $photo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($group)
    {
      $this->group = $group;
      $this->photo = $group->photo;
    }

    public function broadcastAs()
    {
      return 'new-group';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
      $author = $this->group->author;
      return ["group-".spaceWithUnderscore($author->country)."-".spaceWithUnderscore($author->state)];
    }
}

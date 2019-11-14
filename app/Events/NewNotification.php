<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message, $user_id, $count;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $message)
    {
      $this->user_id = $user->user_id;
      $this->message  = "$message";
      $this->count = \App\UserNotificationMessage::unreadCount($user);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
      return ["notifications-{$this->user_id}"];
      // return new PrivateChannel('request');
    }

    public function broadcastAs()
    {
      return 'new-notification';
    }
}

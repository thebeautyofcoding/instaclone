<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReplyPosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $reply;
    /**
     * Create a new event instance.
     */
    public function __construct($reply)
    {
        $this->reply = $reply;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('posts'),
        ];
    }

    public function broadcastWith()
    {
        return [
            'reply' => $this->reply,
        ];
    }
}

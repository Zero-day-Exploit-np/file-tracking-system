<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileTransferred implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transfer;

    public function __construct($transfer)
    {
        $this->transfer = $transfer;
    }

    public function broadcastOn()
    {
        return new Channel('admin-channel');
    }

    public function broadcastAs()
    {
        return 'file.transferred';
    }
}

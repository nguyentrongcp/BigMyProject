<?php
namespace App\Functions;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Pusher implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    private $listenAs;

    public function __construct($listenAs, $message)
    {
        $this->message = $message;
        $this->listenAs = $listenAs;
    }

    public function broadcastOn()
    {
        return ['my-project'];
    }

    public function broadcastAs()
    {
        return $this->listenAs;
    }
}

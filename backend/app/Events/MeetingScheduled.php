<?php

namespace App\Events;

use App\Models\Meeting;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MeetingScheduled implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $meeting;

    public function __construct(Meeting $meeting)
    {
        $meeting->load('invitedUser', 'invitedUser');
        $meeting->load('user', 'user');

        $this->meeting = $meeting;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('users.' . $this->meeting->user_id),
            new PrivateChannel('users.' . $this->meeting->invited_user_id),
        ];
    }

    public function broadcastAs()
    {
        return 'meeting.created';
    }
}

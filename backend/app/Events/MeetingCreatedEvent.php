<?php

namespace App\Events;

use App\Models\Meeting;

class MeetingCreatedEvent extends AbstractMeetingScheduled
{
    public function __construct(Meeting $meeting)
    {
        $this->broadcastName = 'meeting.created';
        parent::__construct($meeting);
    }
}

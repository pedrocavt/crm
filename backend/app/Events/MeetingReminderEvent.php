<?php

namespace App\Events;

use App\Models\Meeting;

class MeetingReminderEvent extends AbstractMeetingScheduled
{
    public function __construct(Meeting $meeting)
    {
        $this->broadcastName = 'meeting.reminder';
        parent::__construct($meeting);
    }
}

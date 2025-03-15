<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendMeetingReminderJob;
use Illuminate\Console\Scheduling\Schedule;

class SendMeetingReminders extends Command
{
    protected $signature = 'meetings:reminders';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        dispatch(new SendMeetingReminderJob());
    }
}

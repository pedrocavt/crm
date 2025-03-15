<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendMeetingReminderJob;
use Illuminate\Console\Scheduling\Schedule;

class SendMeetingReminders extends Command
{
    protected $signature = 'meetings:reminders';
    protected $description = 'Verifica reuniões futuras e envia lembretes';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        dispatch(new SendMeetingReminderJob());
        $this->info('Job de lembrete de reuniões foi executado.');
    }
}

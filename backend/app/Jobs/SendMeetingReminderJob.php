<?php

namespace App\Jobs;

use App\Events\MeetingReminderEvent;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMeetingReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $now = Carbon::now();
        $start = $now->copy();
        $end = $now->copy()->addMinutes(5);
        
        $meetings = Meeting::whereBetween('scheduled_at', [$start, $end])
            ->where('reminder_sent', false)
            ->get();

        foreach ($meetings as $meeting) {
            event(new MeetingReminderEvent($meeting));
            $meeting->reminder_sent = true;
            $meeting->update();
        }
    }
}

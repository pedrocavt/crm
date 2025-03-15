<?php

namespace Tests\Unit\Job;

use Tests\TestCase;
use App\Jobs\SendMeetingReminderJob;
use App\Events\MeetingReminderEvent;
use App\Models\Meeting;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class SendMeetingReminderJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatches_meeting_reminder_event_for_upcoming_meetings()
    {
        Event::fake();

        $meeting = Meeting::factory()->create([
            'scheduled_at' => Carbon::now()->addMinutes(3),
            'reminder_sent' => false
        ]);

        (new SendMeetingReminderJob())->handle();

        Event::assertDispatched(MeetingReminderEvent::class, function ($event) use ($meeting) {
            return $event->meeting->id === $meeting->id;
        });

        $this->assertTrue((bool) Meeting::find($meeting->id)->reminder_sent);
    }
}
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Meeting;
use App\Events\MeetingCreatedEvent;
use App\Events\MeetingDeletedEvent;
use App\Events\MeetingReminderEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MeetingEventTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_dispatches_meeting_created_event()
    {
        Event::fake();
        $user = User::factory()->create();
        $meeting = Meeting::factory()->create([
            'user_id' => $user->id,
            'invited_user_id' => User::factory()->create()->id,
            'scheduled_at' => now()->addDays(1),
            'notes' => 'Reunião teste',
        ]);

        event(new MeetingCreatedEvent($meeting));

        Event::assertDispatched(MeetingCreatedEvent::class, function ($event) use ($meeting) {
            return $event->meeting->id === $meeting->id;
        });
    }

    public function test_dispatches_meeting_reminder_event()
    {
        Event::fake();

        $meeting = Meeting::factory()->create([
            'scheduled_at' => now()->addMinutes(5),
        ]);

        event(new MeetingReminderEvent($meeting));

        Event::assertDispatched(MeetingReminderEvent::class, function ($event) use ($meeting) {
            return $event->meeting->id === $meeting->id;
        });
    }

    public function test_dispatches_meeting_deleted_event()
    {
        Event::fake();

        $meeting = Meeting::factory()->create();
        $meetingData = $meeting;
        $meeting->delete();

        event(new MeetingDeletedEvent($meeting));

        Event::assertDispatched(MeetingDeletedEvent::class, function ($event) use ($meetingData) {
            return $event->meeting->id === $meetingData->id;
        });
    }
}

<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Meeting;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MeetingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $headers;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar um usuário autenticado
        $this->user = User::factory()->create();
        $token = JWTAuth::fromUser($this->user);
        $this->headers = ['Authorization' => "Bearer $token"];
    }

    /** @test */
    public function it_can_create_a_meeting()
    {
        $data = [
            'user_id' => $this->user->id,
            'invited_user_id' => User::factory()->create()->id,
            'scheduled_at' => now()->addHours(2)->format('Y-m-d H:i:s'),
            'notes' => 'Reunião de teste'
        ];

        $response = $this->postJson('/api/meetings', $data, $this->headers);

        $response->assertStatus(201);
        $this->assertDatabaseHas('meetings', $data);
    }

    /** @test */
    public function it_can_list_meetings()
    {
        Meeting::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/meetings', $this->headers);

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_show_a_meeting()
    {
        $meeting = Meeting::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/meetings/{$meeting->id}", $this->headers);

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $meeting->id]);
    }

    /** @test */
    public function it_can_update_a_meeting()
    {
        $meeting = Meeting::factory()->create(['user_id' => $this->user->id]);
        $updateData = ['notes' => 'Nova nota de teste'];

        $response = $this->putJson("/api/meetings/{$meeting->id}", $updateData, $this->headers);

        $response->assertStatus(200);
        $this->assertDatabaseHas('meetings', ['id' => $meeting->id, 'notes' => 'Nova nota de teste']);
    }

    /** @test */
    public function it_can_delete_a_meeting()
    {
        $meeting = Meeting::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/meetings/{$meeting->id}", [], $this->headers);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('meetings', ['id' => $meeting->id]);
    }
}

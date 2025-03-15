<?php

namespace Tests\Unit\Controller;

use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase; // 🔥 Reseta o banco antes de cada teste

    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->app->instance(UserRepositoryInterface::class, $this->userRepository);
    }

    public function test_users_returns_all_users()
    {
        $users = User::factory()->count(3)->create();
        $this->userRepository->shouldReceive('all')->once()->andReturn($users);
        
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->getJson('/api/users', $headers);

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_register_creates_user_and_returns_token()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $hashedPassword = Hash::make($userData['password']);
        $user = User::factory()->make(['password' => $hashedPassword]);

        $this->userRepository->shouldReceive('create')->once()->andReturn($user);
        JWTAuth::shouldReceive('fromUser')->once()->andReturn('fake-jwt-token');

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
                 ->assertJsonStructure(['user', 'token']);
    }

    public function test_login_returns_token_on_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        JWTAuth::shouldReceive('attempt')->once()->andReturn('fake-jwt-token');
        Auth::shouldReceive('user')->once()->andReturn($user);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['user', 'token']);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        JWTAuth::shouldReceive('attempt')->once()->andReturn(false);

        $response = $this->postJson('/api/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Credenciais inválidas']);
    }

    public function test_logout_logs_out_user()
    {
        Auth::shouldReceive('logout')->once();
        Auth::shouldReceive('guard')->andReturnSelf();
        Auth::shouldReceive('check')->once()->andReturnSelf();
        Auth::shouldReceive('shouldUse')->once()->andReturnSelf();

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logout realizado com sucesso.']);
    }

    public function test_refresh_returns_new_token()
    {
        $user = User::factory()->create();
        Auth::shouldReceive('refresh')->once()->andReturn('new-fake-token');
        
        Auth::shouldReceive('guard')->andReturnSelf();
        Auth::shouldReceive('check')->once()->andReturnSelf();
        Auth::shouldReceive('shouldUse')->once()->andReturnSelf();
        Auth::shouldReceive('user')->once()->andReturn($user);

        $response = $this->postJson('/api/refresh');
        $expectedUserData = $user->toArray();
        $expectedUserData['email_verified_at'] = $user->email_verified_at ? $user->email_verified_at->toJSON() : null;
    
        $response->assertStatus(200)
                 ->assertJson([
                     'user' => $expectedUserData,
                     'token' => 'new-fake-token'
                 ]);
    }

    public function test_broadcastingLogin_fails_with_invalid_token()
    {
        JWTAuth::shouldReceive('parseToken')->once()->andThrow(new \Exception());

        $response = $this->postJson('/api/broadcasting/auth');

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthorized']);
    }
}

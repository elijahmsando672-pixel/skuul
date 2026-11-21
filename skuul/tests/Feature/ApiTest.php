<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'API User',
            'email' => 'api-user@example.test',
            'password' => Hash::make('password'),
            'role_type' => 'student',
            'is_active' => true,
        ]);
    }

    public function test_post_login_returns_token(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'email']])
            ->assertJsonPath('user.email', $this->user->email);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_get_user_works_with_bearer_token(): void
    {
        $token = $this->loginAndGetToken();

        $this->withToken($token)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('id', $this->user->id)
            ->assertJsonPath('email', $this->user->email);
    }

    public function test_post_logout_deletes_token(): void
    {
        $token = $this->loginAndGetToken();

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJson(['message' => 'Logged out']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    private function loginAndGetToken(): string
    {
        return $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->json('token');
    }
}

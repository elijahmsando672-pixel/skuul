<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createRoleUser('admin', 'admin-auth@example.test');
    }

    public function test_login_page_loads(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Sign in to your account');
    }

    public function test_valid_login_redirects_to_dashboard(): void
    {
        $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($this->user);
    }

    public function test_invalid_credentials_show_error(): void
    {
        $this->from('/login')
            ->post('/login', [
                'email' => $this->user->email,
                'password' => 'wrong-password',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $this->actingAs($this->user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_logout_works(): void
    {
        $this->actingAs($this->user)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['access_token', 'token_type', 'expires_in', 'user']])
            ->assertJsonPath('data.token_type', 'bearer');
        $this->assertDatabaseHas('users', ['email' => 'new@example.com', 'role' => 'job_seeker']);
    }

    public function test_user_can_register_as_company(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Company Owner',
            'email' => 'company@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'company',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.user.role', 'company');
        $this->assertDatabaseHas('users', ['email' => 'company@example.com', 'role' => 'company']);
    }

    public function test_user_can_login(): void
    {
        User::factory()->create(['email' => 'login@example.com']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'login@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['access_token', 'token_type', 'expires_in']]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_get_me(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_authenticated_user_can_refresh_token(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/refresh');

        $response->assertOk()
            ->assertJsonStructure(['data' => ['access_token', 'token_type', 'expires_in']]);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Successfully logged out');
    }

    public function test_register_validation_requires_valid_email_and_password(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Bad',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_register_rejects_admin_role(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_me_returns_unauthorized_without_token(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertUnauthorized();
    }

    public function test_protected_routes_return_unauthorized_without_token(): void
    {
        $this->getJson('/api/v1/my/applications')->assertUnauthorized();
        $this->postJson('/api/v1/auth/logout')->assertUnauthorized();
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_users(): void
    {
        User::factory()->count(3)->create();
        $admin = User::factory()->admin()->create();
        $token = auth('api')->login($admin);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/users')
            ->assertOk()
            ->assertJsonStructure(['data', 'meta']);
    }

    public function test_non_admin_cannot_list_users(): void
    {
        $employer = User::factory()->company()->create();
        $token = auth('api')->login($employer);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/users')
            ->assertForbidden();
    }
}

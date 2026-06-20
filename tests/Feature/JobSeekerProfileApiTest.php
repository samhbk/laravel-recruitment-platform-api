<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobSeekerProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_seeker_can_upsert_and_view_profile(): void
    {
        $seeker = User::factory()->jobSeeker()->create();
        $token = auth('api')->login($seeker);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/me/profile/job-seeker')
            ->assertNotFound();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/me/profile/job-seeker', [
                'headline' => 'Senior PHP Developer',
                'bio' => 'Laravel and REST APIs.',
                'skills' => ['PHP', 'Laravel', 'MySQL'],
                'preferred_locations' => ['Remote', 'Berlin'],
                'preferred_employment_types' => ['remote', 'full_time'],
            ])
            ->assertCreated()
            ->assertJsonPath('data.headline', 'Senior PHP Developer');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/me/profile/job-seeker')
            ->assertOk()
            ->assertJsonPath('data.skills.0', 'PHP');
    }

    public function test_company_user_cannot_access_job_seeker_profile_routes(): void
    {
        $employer = User::factory()->company()->create();
        $token = auth('api')->login($employer);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/me/profile/job-seeker', ['headline' => 'Nope'])
            ->assertForbidden();
    }
}

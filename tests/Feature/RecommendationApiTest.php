<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\JobSeekerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecommendationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_seeker_receives_recommendations_based_on_profile_skills(): void
    {
        $seeker = User::factory()->jobSeeker()->create();
        JobSeekerProfile::query()->create([
            'user_id' => $seeker->id,
            'skills' => ['PHP', 'Laravel'],
            'preferred_locations' => ['Remote'],
            'preferred_employment_types' => ['remote'],
        ]);

        JobListing::factory()->create([
            'title' => 'PHP Backend Role',
            'skills' => ['PHP', 'Laravel', 'MySQL'],
            'location' => 'Remote',
            'employment_type' => 'remote',
            'is_published' => true,
            'published_at' => now(),
        ]);
        JobListing::factory()->create([
            'title' => 'Unrelated Designer Role',
            'skills' => ['Figma'],
            'location' => 'Munich',
            'employment_type' => 'full_time',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $token = auth('api')->login($seeker);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/me/recommendations');

        $response->assertOk()
            ->assertJsonStructure(['data']);
        $titles = collect($response->json('data'))->pluck('title');
        $this->assertTrue($titles->contains('PHP Backend Role'));
    }

    public function test_company_user_cannot_access_recommendations(): void
    {
        $employer = User::factory()->company()->create();
        $token = auth('api')->login($employer);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/me/recommendations')
            ->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_access_recommendations(): void
    {
        $this->getJson('/api/v1/me/recommendations')
            ->assertUnauthorized();
    }
}

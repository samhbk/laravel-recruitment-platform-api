<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavedJobApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_seeker_can_save_and_list_saved_jobs(): void
    {
        $seeker = User::factory()->jobSeeker()->create();
        $listing = JobListing::factory()->create(['is_published' => true]);
        $token = auth('api')->login($seeker);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/job-listings/'.$listing->id.'/save')
            ->assertCreated()
            ->assertJsonPath('saved', true);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/me/saved-jobs')
            ->assertOk()
            ->assertJsonPath('data.0.id', $listing->id)
            ->assertJsonPath('data.0.is_saved', true);
    }

    public function test_job_seeker_can_unsave_job(): void
    {
        $seeker = User::factory()->jobSeeker()->create();
        $listing = JobListing::factory()->create(['is_published' => true]);
        $token = auth('api')->login($seeker);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/job-listings/'.$listing->id.'/save')
            ->assertCreated();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/job-listings/'.$listing->id.'/save')
            ->assertNoContent();
    }

    public function test_company_user_cannot_save_jobs(): void
    {
        $employer = User::factory()->company()->create();
        $listing = JobListing::factory()->create(['is_published' => true]);
        $token = auth('api')->login($employer);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/job-listings/'.$listing->id.'/save')
            ->assertForbidden();
    }

    public function test_authenticated_seeker_sees_is_saved_on_job_listing_index(): void
    {
        $seeker = User::factory()->jobSeeker()->create();
        $saved = JobListing::factory()->create(['is_published' => true, 'published_at' => now()]);
        $other = JobListing::factory()->create(['is_published' => true, 'published_at' => now()]);
        $token = auth('api')->login($seeker);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/job-listings/'.$saved->id.'/save')
            ->assertCreated();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/job-listings');

        $response->assertOk();
        $rows = collect($response->json('data'))->keyBy('id');
        $this->assertTrue($rows[$saved->id]['is_saved']);
        $this->assertFalse($rows[$other->id]['is_saved']);
    }
}

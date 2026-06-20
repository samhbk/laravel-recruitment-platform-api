<?php

namespace Tests\Feature;

use App\Domain\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationEmployerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_can_apply_to_published_job(): void
    {
        $employer = User::factory()->company()->create();
        $listing = JobListing::factory()->create(['user_id' => $employer->id, 'is_published' => true]);
        $candidate = User::factory()->jobSeeker()->create();
        $token = auth('api')->login($candidate);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/applications', [
                'job_listing_id' => $listing->id,
                'cover_letter' => 'I would like to contribute to your team.',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', 'pending');
        $this->assertDatabaseHas('applications', [
            'job_listing_id' => $listing->id,
            'user_id' => $candidate->id,
        ]);
    }

    public function test_duplicate_application_returns_unprocessable(): void
    {
        $employer = User::factory()->company()->create();
        $listing = JobListing::factory()->create(['user_id' => $employer->id, 'is_published' => true]);
        $candidate = User::factory()->jobSeeker()->create();
        $token = auth('api')->login($candidate);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/applications', [
                'job_listing_id' => $listing->id,
            ])
            ->assertCreated();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/applications', [
                'job_listing_id' => $listing->id,
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'You have already applied to this job.');
    }

    public function test_employer_can_update_application_status(): void
    {
        $employer = User::factory()->company()->create();
        $listing = JobListing::factory()->create(['user_id' => $employer->id, 'is_published' => true]);
        $candidate = User::factory()->jobSeeker()->create();
        $application = Application::factory()->create([
            'job_listing_id' => $listing->id,
            'user_id' => $candidate->id,
            'status' => ApplicationStatus::Pending->value,
        ]);
        $token = auth('api')->login($employer);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/applications/'.$application->id.'/status', [
                'status' => ApplicationStatus::Shortlisted->value,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.status', ApplicationStatus::Shortlisted->value);
    }

    public function test_candidate_cannot_update_application_status(): void
    {
        $employer = User::factory()->company()->create();
        $listing = JobListing::factory()->create(['user_id' => $employer->id, 'is_published' => true]);
        $candidate = User::factory()->jobSeeker()->create();
        $application = Application::factory()->create([
            'job_listing_id' => $listing->id,
            'user_id' => $candidate->id,
        ]);
        $token = auth('api')->login($candidate);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/applications/'.$application->id.'/status', [
                'status' => ApplicationStatus::Hired->value,
            ])
            ->assertStatus(403);
    }

    public function test_employer_cannot_change_status_for_other_companies_listing(): void
    {
        $owner = User::factory()->company()->create();
        $other = User::factory()->company()->create();
        $listing = JobListing::factory()->create(['user_id' => $owner->id, 'is_published' => true]);
        $candidate = User::factory()->jobSeeker()->create();
        $application = Application::factory()->create([
            'job_listing_id' => $listing->id,
            'user_id' => $candidate->id,
        ]);
        $token = auth('api')->login($other);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/applications/'.$application->id.'/status', [
                'status' => ApplicationStatus::Rejected->value,
            ])
            ->assertStatus(403);
    }
}

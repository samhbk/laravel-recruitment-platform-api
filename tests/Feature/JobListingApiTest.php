<?php

namespace Tests\Feature;

use App\Domain\Enums\EmploymentType;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobListingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_list_published_job_listings(): void
    {
        JobListing::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/job-listings');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);
        $this->assertGreaterThanOrEqual(2, count($response->json('data')));
    }

    public function test_guest_can_show_published_job_listing(): void
    {
        $listing = JobListing::factory()->create();

        $response = $this->getJson('/api/v1/job-listings/'.$listing->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', $listing->title);
    }

    public function test_public_index_filters_by_employment_type(): void
    {
        JobListing::factory()->create([
            'employment_type' => EmploymentType::Remote,
            'is_published' => true,
            'published_at' => now(),
        ]);
        JobListing::factory()->create([
            'employment_type' => EmploymentType::FullTime,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/job-listings?employment_type=remote');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        foreach ($data as $row) {
            $this->assertSame('remote', $row['employment_type']);
        }
    }

    public function test_job_seeker_cannot_create_job_listing(): void
    {
        $user = User::factory()->jobSeeker()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/job-listings', [
                'title' => 'Should fail',
                'description' => 'No access.',
                'company_name' => 'X',
                'location' => 'Remote',
                'employment_type' => 'remote',
            ]);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_create_job_listing(): void
    {
        $user = User::factory()->company()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/job-listings', [
                'title' => 'Senior PHP Developer',
                'description' => 'We are hiring.',
                'company_name' => 'Demo Co BV',
                'location' => 'Remote',
                'employment_type' => 'full_time',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Senior PHP Developer');
        $this->assertDatabaseHas('job_listings', ['title' => 'Senior PHP Developer', 'user_id' => $user->id]);
    }

    public function test_owner_can_update_job_listing(): void
    {
        $user = User::factory()->create();
        $listing = JobListing::factory()->create(['user_id' => $user->id]);
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/job-listings/'.$listing->id, [
                'title' => 'Updated Title',
                'description' => $listing->description,
                'company_name' => $listing->company_name,
                'location' => $listing->location,
                'employment_type' => $listing->employment_type,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Title');
    }

    public function test_owner_can_delete_job_listing(): void
    {
        $user = User::factory()->company()->create();
        $listing = JobListing::factory()->create(['user_id' => $user->id]);
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/job-listings/'.$listing->id);

        $response->assertStatus(204);
        $this->assertSoftDeleted('job_listings', ['id' => $listing->id]);
    }

    public function test_guest_cannot_view_unpublished_job_listing(): void
    {
        $listing = JobListing::factory()->create(['is_published' => false]);

        $this->getJson('/api/v1/job-listings/'.$listing->id)
            ->assertNotFound();
    }

    public function test_create_job_listing_validation_fails_without_required_fields(): void
    {
        $user = User::factory()->company()->create();
        $token = auth('api')->login($user);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/job-listings', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description', 'company_name', 'location', 'employment_type']);
    }
}

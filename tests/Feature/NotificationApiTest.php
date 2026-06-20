<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_receives_notification_when_candidate_applies(): void
    {
        $employer = User::factory()->company()->create();
        $listing = JobListing::factory()->create(['user_id' => $employer->id, 'is_published' => true]);
        $candidate = User::factory()->jobSeeker()->create();
        $token = auth('api')->login($candidate);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/applications', ['job_listing_id' => $listing->id])
            ->assertCreated();

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $employer->id,
        ]);
    }

    public function test_user_can_list_and_mark_notifications_read(): void
    {
        $employer = User::factory()->company()->create();
        $listing = JobListing::factory()->create(['user_id' => $employer->id, 'is_published' => true]);
        $candidate = User::factory()->jobSeeker()->create();

        $this->actingAs($candidate, 'api')
            ->postJson('/api/v1/applications', ['job_listing_id' => $listing->id])
            ->assertCreated();

        $token = auth('api')->login($employer);
        $index = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/notifications')
            ->assertOk();

        $notificationId = $index->json('data.0.id');
        $this->assertNotNull($notificationId);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/notifications/'.$notificationId.'/read')
            ->assertNoContent();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/notifications/read-all')
            ->assertOk();
    }
}

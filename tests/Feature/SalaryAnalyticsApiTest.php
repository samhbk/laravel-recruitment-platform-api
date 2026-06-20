<?php

namespace Tests\Feature;

use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalaryAnalyticsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_fetch_salary_analytics(): void
    {
        JobListing::factory()->create([
            'salary_min' => 50000,
            'salary_max' => 80000,
            'employment_type' => 'full_time',
            'is_published' => true,
        ]);

        $response = $this->getJson('/api/v1/salary-analytics');

        $response->assertOk()
            ->assertJsonStructure(['overview', 'by_employment_type'])
            ->assertJsonPath('overview.total_listings_with_salary', 1);
    }
}

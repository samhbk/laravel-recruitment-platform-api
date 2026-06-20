<?php

namespace Tests\Unit;

use App\Actions\SalaryAnalytics\GetSalaryAnalyticsAction;
use App\Domain\Repositories\SalaryAnalyticsFilters;
use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalaryAnalyticsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_overview_and_by_employment_type(): void
    {
        JobListing::factory()->create([
            'salary_min' => 50000,
            'salary_max' => 80000,
            'employment_type' => 'full_time',
        ]);
        JobListing::factory()->create([
            'salary_min' => 60000,
            'salary_max' => 90000,
            'employment_type' => 'full_time',
        ]);

        $action = app(GetSalaryAnalyticsAction::class);
        $result = $action->execute(SalaryAnalyticsFilters::fromArray([]));

        $this->assertArrayHasKey('overview', $result);
        $this->assertArrayHasKey('by_employment_type', $result);
        $this->assertEquals(2, $result['overview']['total_listings_with_salary']);
        $this->assertArrayHasKey('full_time', $result['by_employment_type']);
    }
}

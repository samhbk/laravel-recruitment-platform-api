<?php

namespace Database\Factories;

use App\Domain\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_listing_id' => JobListing::factory(),
            'user_id' => User::factory(),
            'status' => fake()->randomElement(ApplicationStatus::values()),
            'cover_letter' => fake()->paragraph(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Domain\Enums\EmploymentType;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<JobListing>
 */
class JobListingFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->jobTitle();

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->paragraphs(3, true),
            'company_name' => fake()->company(),
            'location' => fake()->city().', '.fake()->countryCode(),
            'employment_type' => fake()->randomElement(EmploymentType::values()),
            'salary_min' => fake()->numberBetween(40000, 80000),
            'salary_max' => fake()->numberBetween(90000, 150000),
            'salary_currency' => 'USD',
            'is_published' => true,
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Domain\Enums\EmploymentType;
use App\Domain\Enums\UserRole;
use App\Models\Application;
use App\Models\Company;
use App\Models\JobListing;
use App\Models\JobSeekerProfile;
use App\Models\SavedJob;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Synthetic demo data only — safe for public GitHub / portfolio screenshots.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Demo Administrator',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
            ]
        );

        $employer = User::query()->updateOrCreate(
            ['email' => 'employer@example.com'],
            [
                'name' => 'Demo Employer',
                'password' => Hash::make('password'),
                'role' => UserRole::Company,
            ]
        );

        $company = Company::query()->updateOrCreate(
            ['slug' => 'demo-employer-gmbh'],
            [
                'user_id' => $employer->id,
                'name' => 'Demo Employer GmbH',
                'description' => 'Fictitious company for API demonstrations (not affiliated with any real organisation).',
                'website' => 'https://demo-employer.example',
                'industry' => 'Software',
                'company_size' => '51-200',
                'headquarters_location' => 'Berlin, DE',
                'is_verified' => true,
            ]
        );

        $candidate = User::query()->updateOrCreate(
            ['email' => 'candidate@example.com'],
            [
                'name' => 'Demo Candidate',
                'password' => Hash::make('password'),
                'role' => UserRole::JobSeeker,
            ]
        );

        JobSeekerProfile::query()->updateOrCreate(
            ['user_id' => $candidate->id],
            [
                'headline' => 'Senior PHP Engineer',
                'bio' => 'Reference profile: Laravel, APIs, relational data.',
                'skills' => ['PHP', 'Laravel', 'MySQL', 'Redis', 'REST'],
                'preferred_locations' => ['Remote', 'Berlin'],
                'preferred_employment_types' => [EmploymentType::FullTime->value, EmploymentType::Remote->value],
            ]
        );

        $listingDefs = [
            [
                'slug' => 'demo-backend-engineer-apis',
                'title' => 'Backend Engineer (APIs)',
                'skills' => ['PHP', 'Laravel', 'MySQL'],
                'location' => 'Remote',
                'employment_type' => EmploymentType::Remote,
            ],
            [
                'slug' => 'demo-full-stack-developer',
                'title' => 'Full Stack Developer',
                'skills' => ['TypeScript', 'Vue', 'PHP'],
                'location' => 'Berlin, DE',
                'employment_type' => EmploymentType::FullTime,
            ],
            [
                'slug' => 'demo-platform-engineer',
                'title' => 'Platform Engineer',
                'skills' => ['Docker', 'Kubernetes', 'AWS'],
                'location' => 'Hamburg, DE',
                'employment_type' => EmploymentType::FullTime,
            ],
        ];

        $listings = collect($listingDefs)->map(function (array $row) use ($employer, $company) {
            return JobListing::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'user_id' => $employer->id,
                    'company_id' => $company->id,
                    'title' => $row['title'],
                    'description' => implode("\n\n", fake()->paragraphs(3, false)),
                    'skills' => $row['skills'],
                    'company_name' => $company->name,
                    'location' => $row['location'],
                    'employment_type' => $row['employment_type'],
                    'salary_min' => 70000,
                    'salary_max' => 110000,
                    'salary_currency' => 'EUR',
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        });

        $first = $listings->first();
        Application::query()->updateOrCreate(
            [
                'job_listing_id' => $first->id,
                'user_id' => $candidate->id,
            ],
            [
                'status' => 'pending',
                'cover_letter' => 'Demo application — portfolio seed data.',
            ]
        );

        SavedJob::query()->updateOrCreate(
            [
                'user_id' => $candidate->id,
                'job_listing_id' => $listings->get(1)->id,
            ],
            []
        );

        JobListing::factory()->count(5)->create(['user_id' => $employer->id]);

        $this->command?->info('Demo users (local / Docker only, not production):');
        $this->command?->table(
            ['Email', 'Password', 'Role'],
            [
                ['admin@example.com', 'password', 'admin'],
                ['employer@example.com', 'password', 'company'],
                ['candidate@example.com', 'password', 'job_seeker'],
            ],
        );
    }
}

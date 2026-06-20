<?php

declare(strict_types=1);

namespace App\Services\Recommendations;

use App\Domain\Repositories\JobSeekerProfileRepositoryInterface;
use App\Models\Application;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Support\Collection;

final readonly class JobRecommendationService
{
    public function __construct(
        private JobSeekerProfileRepositoryInterface $jobSeekerProfiles,
    ) {}

    /**
     * @return Collection<int, JobListing>
     */
    public function forUser(User $user, int $limit = 20): Collection
    {
        $profile = $this->jobSeekerProfiles->findByUserId($user->id);

        if ($profile !== null) {
            return $this->fromProfile($profile->skills ?? [], $profile->preferred_locations ?? [], $profile->preferred_employment_types ?? [], $limit);
        }

        return $this->coldStartForUser($user, $limit);
    }

    /**
     * @param  list<string>  $skills
     * @param  list<string>  $preferredLocations
     * @param  list<string>  $preferredEmploymentTypes
     * @return Collection<int, JobListing>
     */
    private function fromProfile(array $skills, array $preferredLocations, array $preferredEmploymentTypes, int $limit): Collection
    {
        $skills = array_values(array_filter(array_map('strtolower', $skills)));
        $preferredLocations = array_values(array_filter(array_map('strtolower', $preferredLocations)));

        $candidates = JobListing::query()
            ->where('is_published', true)
            ->with(['user:id,name,role', 'company'])
            ->latest('published_at')
            ->limit(150)
            ->get();

        return $candidates
            ->map(function (JobListing $job) use ($skills, $preferredLocations, $preferredEmploymentTypes) {
                $score = 0;
                $jobSkills = array_map('strtolower', $job->skills ?? []);
                foreach ($skills as $needle) {
                    foreach ($jobSkills as $haystack) {
                        if ($needle === '' || $haystack === '') {
                            continue;
                        }
                        if ($haystack === $needle || str_contains($haystack, $needle) || str_contains($needle, $haystack)) {
                            $score += 2;
                            break;
                        }
                    }
                }

                $loc = strtolower($job->location);
                foreach ($preferredLocations as $pref) {
                    if ($pref !== '' && str_contains($loc, $pref)) {
                        $score += 1;
                        break;
                    }
                }

                if ($preferredEmploymentTypes !== [] && in_array($job->employment_type->value, $preferredEmploymentTypes, true)) {
                    $score += 1;
                }

                return ['job' => $job, 'score' => $score];
            })
            ->sortByDesc('score')
            ->take($limit)
            ->pluck('job')
            ->values();
    }

    /**
     * @return Collection<int, JobListing>
     */
    private function coldStartForUser(User $user, int $limit): Collection
    {
        $appliedLocations = Application::query()
            ->where('user_id', $user->id)
            ->with('jobListing')
            ->latest()
            ->limit(15)
            ->get()
            ->pluck('jobListing.location')
            ->filter()
            ->unique()
            ->values();

        $query = JobListing::query()
            ->where('is_published', true)
            ->with(['user:id,name,role', 'company']);

        if ($appliedLocations->isNotEmpty()) {
            $query->where(function ($q) use ($appliedLocations) {
                foreach ($appliedLocations as $loc) {
                    $q->orWhere('location', 'like', '%'.$loc.'%');
                }
            });
        }

        return $query->latest('published_at')->limit($limit)->get();
    }
}

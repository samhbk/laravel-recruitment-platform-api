<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\RegisterUserAction;
use App\Domain\Repositories\ApplicationRepositoryInterface;
use App\Domain\Repositories\CompanyRepositoryInterface;
use App\Domain\Repositories\JobListingRepositoryInterface;
use App\Domain\Repositories\JobSeekerProfileRepositoryInterface;
use App\Domain\Repositories\SalaryAnalyticsRepositoryInterface;
use App\Domain\Repositories\SavedJobRepositoryInterface;
use App\Infrastructure\Persistence\EloquentApplicationRepository;
use App\Infrastructure\Persistence\EloquentCompanyRepository;
use App\Infrastructure\Persistence\EloquentJobListingRepository;
use App\Infrastructure\Persistence\EloquentJobSeekerProfileRepository;
use App\Infrastructure\Persistence\EloquentSalaryAnalyticsRepository;
use App\Infrastructure\Persistence\EloquentSavedJobRepository;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(JobListingRepositoryInterface::class, EloquentJobListingRepository::class);
        $this->app->bind(ApplicationRepositoryInterface::class, EloquentApplicationRepository::class);
        $this->app->bind(SalaryAnalyticsRepositoryInterface::class, EloquentSalaryAnalyticsRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, EloquentCompanyRepository::class);
        $this->app->bind(SavedJobRepositoryInterface::class, EloquentSavedJobRepository::class);
        $this->app->bind(JobSeekerProfileRepositoryInterface::class, EloquentJobSeekerProfileRepository::class);

        $this->app->when(RegisterUserAction::class)
            ->needs(Guard::class)
            ->give(fn () => $this->app->make('auth')->guard('api'));

        $this->app->when(LoginUserAction::class)
            ->needs(Guard::class)
            ->give(fn () => $this->app->make('auth')->guard('api'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('notification', function (string $value) {
            $user = auth('api')->user();
            if ($user === null) {
                abort(401);
            }

            return $user->notifications()->whereKey($value)->firstOrFail();
        });
    }
}

<?php

use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\JobListingController;
use App\Http\Controllers\Api\JobSeekerProfileController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\Api\SalaryAnalyticsController;
use App\Http\Controllers\Api\SavedJobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (versioned)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('auth/register', [AuthController::class, 'register']);
        Route::post('auth/login', [AuthController::class, 'login']);
    });

    Route::middleware('jwt.optional')->group(function () {
        Route::get('job-listings', [JobListingController::class, 'index']);
        Route::get('job-listings/{jobListing}', [JobListingController::class, 'show']);
    });
    Route::get('salary-analytics', [SalaryAnalyticsController::class, 'index']);
    Route::get('companies/{company}', [CompanyController::class, 'show']);

    Route::middleware('auth:api')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::get('notifications', [NotificationController::class, 'index']);
        Route::patch('notifications/{notification}/read', [NotificationController::class, 'markRead']);
        Route::post('notifications/read-all', [NotificationController::class, 'markAllRead']);

        Route::middleware('role:company')->group(function () {
            Route::post('companies', [CompanyController::class, 'store']);
            Route::get('companies/me/profile', [CompanyController::class, 'me']);
            Route::put('companies/me/profile', [CompanyController::class, 'updateMe']);
        });

        Route::middleware('role:job_seeker,admin')->group(function () {
            Route::get('me/saved-jobs', [SavedJobController::class, 'index']);
            Route::post('job-listings/{jobListing}/save', [SavedJobController::class, 'store']);
            Route::delete('job-listings/{jobListing}/save', [SavedJobController::class, 'destroy']);

            Route::get('me/profile/job-seeker', [JobSeekerProfileController::class, 'show']);
            Route::put('me/profile/job-seeker', [JobSeekerProfileController::class, 'update']);

            Route::get('me/recommendations', [RecommendationController::class, 'index']);
        });

        Route::apiResource('job-listings', JobListingController::class)->except(['index', 'show']);
        Route::get('my/job-listings', [JobListingController::class, 'myListings']);
        Route::get('my/applications', [ApplicationController::class, 'myApplications']);
        Route::apiResource('applications', ApplicationController::class)->only(['index', 'store', 'show']);
        Route::patch('applications/{application}/status', [ApplicationController::class, 'updateStatus']);
        Route::get('job-listings/{jobListing}/applications', [ApplicationController::class, 'listingApplications']);

        Route::middleware('role:admin')->group(function () {
            Route::get('admin/users', [AdminUserController::class, 'index']);
        });
    });
});

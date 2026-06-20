<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Domain\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Get the identifier that will be stored in the JWT subject claim.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array of custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'role' => $this->role->value,
        ];
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    public function jobSeekerProfile(): HasOne
    {
        return $this->hasOne(JobSeekerProfile::class);
    }

    /**
     * Job listings posted by this user (employer).
     */
    public function jobListings(): HasMany
    {
        return $this->hasMany(JobListing::class);
    }

    /**
     * Applications submitted by this user (candidate).
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function savedJobListings(): BelongsToMany
    {
        return $this->belongsToMany(JobListing::class, 'saved_jobs')
            ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isCompany(): bool
    {
        return $this->role === UserRole::Company;
    }

    public function isJobSeeker(): bool
    {
        return $this->role === UserRole::JobSeeker;
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enums\EmploymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JobListing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_id',
        'title',
        'slug',
        'description',
        'skills',
        'company_name',
        'location',
        'employment_type',
        'salary_min',
        'salary_max',
        'salary_currency',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'employment_type' => EmploymentType::class,
            'skills' => 'array',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public static function booted(): void
    {
        static::creating(function (JobListing $listing) {
            if (empty($listing->slug)) {
                $listing->slug = Str::slug($listing->title);
            }
        });
    }
}

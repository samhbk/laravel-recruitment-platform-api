<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'website',
        'logo_url',
        'industry',
        'company_size',
        'headquarters_location',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function booted(): void
    {
        static::creating(function (Company $company) {
            if (empty($company->slug)) {
                $company->slug = $company->uniqueSlug(Str::slug($company->name));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobListings(): HasMany
    {
        return $this->hasMany(JobListing::class);
    }

    private function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $n = 0;
        while (static::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.(++$n);
        }

        return $slug;
    }
}

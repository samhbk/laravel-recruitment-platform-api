<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobSeekerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'headline',
        'bio',
        'skills',
        'preferred_locations',
        'preferred_employment_types',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'preferred_locations' => 'array',
            'preferred_employment_types' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

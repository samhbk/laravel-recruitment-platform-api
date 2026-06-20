<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_listing_id',
        'user_id',
        'status',
        'cover_letter',
        'resume_path',
        'reviewed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

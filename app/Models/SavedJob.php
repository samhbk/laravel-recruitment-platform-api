<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedJob extends Model
{
    protected $fillable = [
        'user_id',
        'job_listing_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }
}

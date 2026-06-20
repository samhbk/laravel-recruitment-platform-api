<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum ApplicationStatus: string
{
    case Pending = 'pending';
    case Reviewed = 'reviewed';
    case Shortlisted = 'shortlisted';
    case Rejected = 'rejected';
    case Hired = 'hired';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

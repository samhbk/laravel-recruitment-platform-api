<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Company = 'company';
    case JobSeeker = 'job_seeker';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function registerable(): array
    {
        return [self::JobSeeker->value, self::Company->value];
    }
}

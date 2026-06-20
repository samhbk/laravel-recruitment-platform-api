<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum EmploymentType: string
{
    case FullTime = 'full_time';
    case PartTime = 'part_time';
    case Contract = 'contract';
    case Remote = 'remote';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

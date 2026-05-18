<?php

namespace App\Enums;

enum GradingLevelType: string
{
    case Scholastic = 'scholastic';
    case CoScholastic = 'co_scholastic';

    public function label(): string
    {
        return match ($this) {
            self::Scholastic => 'Scholastic',
            self::CoScholastic => 'Co-Scholastic',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Scholastic => 'info',
            self::CoScholastic => 'success',
        };
    }
}

<?php

namespace App\Enums;

enum ExamGroupType: string
{
    case FA = 'fa';
    case SA = 'sa';

    public function label(): string
    {
        return match ($this) {
            self::FA => 'Formative Assessment (FA)',
            self::SA => 'Summative Assessment (SA)',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::FA => 'FA',
            self::SA => 'SA',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::FA => 'info',
            self::SA => 'warning',
        };
    }
}

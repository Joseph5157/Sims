<?php

namespace App\Enums;

enum StudentStatus: string
{
    case Active = 'active';
    case Alumni = 'alumni';
    case Transferred = 'transferred';
    case Dropped = 'dropped';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Alumni => 'Alumni',
            self::Transferred => 'Transferred',
            self::Dropped => 'Dropped',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Alumni => 'info',
            self::Transferred => 'warning',
            self::Dropped => 'danger',
        };
    }
}

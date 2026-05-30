<?php

namespace App\Enums;

enum EventTarget: string
{
    case All = 'all';
    case SpecificClass = 'specific_class';

    public function label(): string
    {
        return match ($this) {
            self::All => 'All Students',
            self::SpecificClass => 'Specific Class',
        };
    }
}

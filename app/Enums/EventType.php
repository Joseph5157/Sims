<?php

namespace App\Enums;

enum EventType: string
{
    case Academic = 'academic';
    case Cultural = 'cultural';
    case Sports = 'sports';
    case General = 'general';

    public function label(): string
    {
        return match ($this) {
            self::Academic => 'Academic',
            self::Cultural => 'Cultural',
            self::Sports => 'Sports',
            self::General => 'General',
        };
    }
}

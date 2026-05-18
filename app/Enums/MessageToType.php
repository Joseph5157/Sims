<?php

namespace App\Enums;

enum MessageToType: string
{
    case Individual = 'individual';
    case Class = 'class';
    case All = 'all';

    public function label(): string
    {
        return match ($this) {
            self::Individual => 'Individual',
            self::class => 'Class',
            self::All => 'All',
        };
    }
}

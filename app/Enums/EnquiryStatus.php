<?php

namespace App\Enums;

enum EnquiryStatus: string
{
    case New = 'new';
    case Followup = 'followup';
    case Converted = 'converted';
    case Dropped = 'dropped';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Followup => 'Follow-Up',
            self::Converted => 'Converted',
            self::Dropped => 'Dropped',
        };
    }
}

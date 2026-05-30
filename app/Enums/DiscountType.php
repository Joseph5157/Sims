<?php

namespace App\Enums;

enum DiscountType: string
{
    case Scholarship = 'scholarship';
    case Sibling = 'sibling';
    case StaffWard = 'staff_ward';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Scholarship => 'Scholarship',
            self::Sibling => 'Sibling',
            self::StaffWard => 'Staff Ward',
            self::Other => 'Other',
        };
    }
}

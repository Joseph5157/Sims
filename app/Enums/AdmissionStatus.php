<?php

namespace App\Enums;

enum AdmissionStatus: string
{
    case Applied = 'applied';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Enrolled = 'enrolled';

    public function label(): string
    {
        return match ($this) {
            self::Applied => 'Applied',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Enrolled => 'Enrolled',
        };
    }
}

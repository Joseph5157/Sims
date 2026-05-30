<?php

namespace App\Enums;

enum EnquirySource: string
{
    case WalkIn = 'walkin';
    case Referral = 'referral';
    case Online = 'online';
    case Phone = 'phone';

    public function label(): string
    {
        return match ($this) {
            self::WalkIn => 'Walk-In',
            self::Referral => 'Referral',
            self::Online => 'Online',
            self::Phone => 'Phone',
        };
    }
}

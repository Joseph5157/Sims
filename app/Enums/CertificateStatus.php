<?php

namespace App\Enums;

enum CertificateStatus: string
{
    case Requested = 'requested';
    case Approved = 'approved';
    case Issued = 'issued';

    public function label(): string
    {
        return match ($this) {
            self::Requested => 'Requested',
            self::Approved => 'Approved',
            self::Issued => 'Issued',
        };
    }
}

<?php

namespace App\Enums;

enum PaymentMode: string
{
    case Cash = 'cash';
    case Online = 'online';
    case Cheque = 'cheque';
    case DD = 'dd';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::Online => 'Online',
            self::Cheque => 'Cheque',
            self::DD => 'Demand Draft',
        };
    }
}

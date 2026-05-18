<?php

namespace App\Enums;

enum CertificateType: string
{
    case TC = 'tc';
    case Bonafide = 'bonafide';
    case Study = 'study';
    case Character = 'character';

    public function label(): string
    {
        return match ($this) {
            self::TC => 'Transfer Certificate',
            self::Bonafide => 'Bonafide Certificate',
            self::Study => 'Study Certificate',
            self::Character => 'Character Certificate',
        };
    }
}

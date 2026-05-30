<?php

namespace App\Enums;

enum GradingType: string
{
    case Marks = 'marks';
    case Grade = 'grade';
    case PassFail = 'pass_fail';

    public function label(): string
    {
        return match ($this) {
            self::Marks => 'Marks',
            self::Grade => 'Grade',
            self::PassFail => 'Pass / Fail',
        };
    }
}

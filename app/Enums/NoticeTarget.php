<?php

namespace App\Enums;

enum NoticeTarget: string
{
    case All = 'all';
    case Faculty = 'faculty';
    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::All => 'All',
            self::Faculty => 'Faculty',
            self::Student => 'Students',
        };
    }
}

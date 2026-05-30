<?php

namespace App\Enums;

enum SubjectType: string
{
    case Theory = 'theory';
    case Practical = 'practical';
    case Elective = 'elective';
    case Project = 'project';

    public function label(): string
    {
        return match ($this) {
            self::Theory => 'Theory',
            self::Practical => 'Practical',
            self::Elective => 'Elective',
            self::Project => 'Project',
        };
    }
}

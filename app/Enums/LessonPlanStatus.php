<?php

namespace App\Enums;

enum LessonPlanStatus: string
{
    case Planned = 'planned';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'Planned',
            self::Completed => 'Completed',
        };
    }
}

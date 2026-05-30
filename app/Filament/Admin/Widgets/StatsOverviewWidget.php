<?php

namespace App\Filament\Admin\Widgets;

use App\Models\CollegeClass;
use App\Models\Department;
use App\Models\DisciplineCase;
use App\Models\Faculty;
use App\Models\Notice;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Students', Student::query()->count())
                ->icon('heroicon-o-users')
                ->color('blue'),

            Stat::make('Total Faculty', Faculty::query()->count())
                ->icon('heroicon-o-academic-cap')
                ->color('teal'),

            Stat::make('Total Departments', Department::query()->count())
                ->icon('heroicon-o-building-library')
                ->color('amber'),

            Stat::make('Total Courses', CollegeClass::query()->count())
                ->icon('heroicon-o-rectangle-stack')
                ->color('purple'),

            Stat::make(
                'Active Notices',
                Notice::query()
                    ->where(function ($query): void {
                        $query
                            ->whereNull('published_at')
                            ->orWhere('published_at', '<=', now());
                    })
                    ->count(),
            )
                ->icon('heroicon-o-megaphone')
                ->color('orange'),

            Stat::make(
                'Discipline Cases',
                DisciplineCase::query()
                    ->where('status', '!=', 'resolved')
                    ->count(),
            )
                ->icon('heroicon-o-exclamation-triangle')
                ->color('red'),
        ];
    }
}

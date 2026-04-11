<?php

namespace App\Filament\Faculty\Widgets;

use App\Models\Attendance;
use App\Models\DisciplineCase;
use App\Models\Student;
use App\Models\TimetableSlot;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class FacultyStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $facultyId = $user?->facultyProfile?->id;

        $classIds = [];

        if ($facultyId) {
            $classIds = TimetableSlot::query()
                ->where('faculty_id', $facultyId)
                ->distinct()
                ->pluck('college_class_id')
                ->all();
        }

        $myClassesCount = count($classIds);

        $myStudentsCount = $classIds
            ? Student::query()->whereIn('college_class_id', $classIds)->count()
            : 0;

        $attendanceMarkedTodayCount = $user
            ? Attendance::query()
                ->where('marked_by', $user->id)
                ->whereDate('attendance_date', today())
                ->count()
            : 0;

        $pendingDisciplineCasesCount = $facultyId
            ? DisciplineCase::query()
                ->where('faculty_id', $facultyId)
                ->where('status', '!=', 'Resolved')
                ->count()
            : 0;

        return [
            Stat::make('My Classes', $myClassesCount)
                ->icon('heroicon-o-rectangle-stack')
                ->color('blue'),

            Stat::make('My Students', $myStudentsCount)
                ->icon('heroicon-o-users')
                ->color('teal'),

            Stat::make('Attendance Marked Today', $attendanceMarkedTodayCount)
                ->icon('heroicon-o-check-circle')
                ->color('green'),

            Stat::make('Pending Discipline Cases', $pendingDisciplineCasesCount)
                ->icon('heroicon-o-exclamation-triangle')
                ->color('red'),
        ];
    }
}

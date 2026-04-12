<?php

namespace App\Filament\Admin\Widgets;

use App\Models\DisciplineCase;
use App\Models\Exam;
use App\Models\Student;
use Filament\Widgets\Widget;

class RecentActivityWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.recent-activity';

    protected static ?int $sort = 3;

    protected function getViewData(): array
    {
        return [
            'recentStudents' => Student::query()
                ->with('user')
                ->latest()
                ->take(5)
                ->get(),

            'recentDisciplineCases' => DisciplineCase::query()
                ->with(['student.user'])
                ->latest()
                ->take(5)
                ->get(),

            'upcomingExams' => Exam::query()
                ->with(['subject', 'examGroup'])
                ->whereNotNull('date')
                ->whereDate('date', '>=', today())
                ->orderBy('date')
                ->orderBy('id')
                ->take(5)
                ->get(),
        ];
    }
}

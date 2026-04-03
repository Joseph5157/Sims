<?php

namespace App\Filament\Student\Pages;

use App\Models\Notice;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class StudentDashboard extends Page
{
    protected string $view = 'filament.student.pages.student-dashboard';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    public function getHeading(): string|Htmlable
    {
        return 'Welcome back, ' . (Auth::user()?->name ?? 'Student');
    }

    public function getSubheading(): string|Htmlable
    {
        return 'Here\'s your academic overview';
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        $student = $user?->studentProfile;

        return [
            'student' => $student,
            'recentGrades' => $student ? $student->grades()->latest()->take(5)->get() : collect(),
            'recentNotices' => Notice::where('expires_at', '>', now())
                ->orWhereNull('expires_at')
                ->latest()
                ->take(5)
                ->get(),
            'todayTimetable' => [], // We'll fill this in later
        ];
    }
}

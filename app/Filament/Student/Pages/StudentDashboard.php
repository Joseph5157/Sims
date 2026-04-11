<?php

namespace App\Filament\Student\Pages;

use App\Models\Notice;
use App\Models\TimetableSlot;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class StudentDashboard extends Page
{
    protected string $view = 'filament.student.pages.student-dashboard';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    public function getHeading(): ?string
    {
        return null;
    }

    public function getSubheading(): ?string
    {
        return null;
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && method_exists($user, 'hasRole') && $user->hasRole('student');
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        $student = $user?->studentProfile;

        return [
            'student' => $student,
            'recentGrades' => $student ? $student->grades()->with('subject')->latest()->take(5)->get() : collect(),
            'recentNotices' => Notice::where('expires_at', '>', now())
                ->orWhereNull('expires_at')
                ->latest()
                ->take(5)
                ->get(),
            'todayTimetable' => $student
                ? TimetableSlot::where('college_class_id', $student->college_class_id)
                    ->where('day', Carbon::now()->format('l'))
                    ->with(['subject', 'faculty.user'])
                    ->orderBy('period')
                    ->get()
                : collect(),
        ];
    }
}

<?php

namespace App\Filament\Parent\Pages;

use App\Models\Guardian;
use App\Models\Notice;
use App\Models\TimetableSlot;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class ParentDashboard extends Page
{
    protected string $view = 'filament.parent.pages.parent-dashboard';

    protected Width|string|null $maxContentWidth = Width::Full;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    public static function getRoutePath(Panel $panel): string
    {
        return '/';
    }

    public static function getRelativeRouteName(Panel $panel): string
    {
        return 'dashboard';
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return null;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('parent') ?? false;
    }

    public function getViewData(): array
    {
        $user = Auth::user();

        // Verify user is authorized and is a guardian
        if (!$user || !$user->hasRole('parent')) {
            return [
                'guardian' => null,
                'student' => null,
                'attendancePercentage' => 0,
                'recentGrades' => collect(),
                'activeNotices' => collect(),
                'todayTimetable' => collect(),
            ];
        }

        // Fetch guardian record associated with the current authenticated user
        $guardian = Guardian::where('user_id', $user->id)
            ->with('student.user', 'student.grades.subject', 'student.attendances', 'student.collegeClass')
            ->first();

        $student = $guardian?->student;
        $attendancePercentage = $student?->getAttendancePercentage() ?? 0;
        $recentGrades = $student
            ? $student->grades
                ->sortByDesc('created_at')
                ->take(5)
                ->values()
            : collect();
        $activeNotices = Notice::where('expires_at', '>', now())
            ->orWhereNull('expires_at')
            ->latest()
            ->take(5)
            ->get();
        $todayTimetable = $student
            ? TimetableSlot::where('college_class_id', $student->college_class_id)
                ->where('day', Carbon::now()->format('l'))
                ->with(['subject', 'faculty.user'])
                ->orderBy('period')
                ->get()
            : collect();

        return [
            'guardian' => $guardian,
            'student' => $student,
            'attendancePercentage' => $attendancePercentage,
            'recentGrades' => $recentGrades,
            'activeNotices' => $activeNotices,
            'todayTimetable' => $todayTimetable,
        ];
    }
}

<?php

namespace App\Filament\Student\Pages;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MyAttendance extends Page
{
    protected string $view = 'filament.student.pages.my-attendance';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'My Attendance';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'My Attendance';

    public string $activeTab = 'yearly';

    public int $selectedMonth;

    public int $selectedYear;

    public bool $hasProfile = false;

    public bool $hasRecords = false;

    // Yearly view
    public array $yearlyStats = [];

    public array $monthlyBreakdown = [];

    // Monthly view
    public array $calendarDays = [];

    public array $monthStats = [];

    public int $startPadding = 0;

    public string $calendarTitle = '';

    // --------------------------------------------------------------------------
    // Lifecycle
    // --------------------------------------------------------------------------

    public function mount(): void
    {
        $this->selectedMonth = (int) now()->month;
        $this->selectedYear = (int) now()->year;

        /** @var User $user */
        $user = Auth::user();
        $student = $user?->studentProfile;

        if (! $student) {
            return;
        }

        $this->hasProfile = true;
        $this->loadYearlyData($student);
        $this->loadMonthlyData($student);
    }

    public function updatedSelectedMonth(): void
    {
        $student = Auth::user()?->studentProfile;

        if ($student) {
            $this->loadMonthlyData($student);
        }
    }

    public function updatedSelectedYear(): void
    {
        $student = Auth::user()?->studentProfile;

        if ($student) {
            $this->loadMonthlyData($student);
        }
    }

    // --------------------------------------------------------------------------
    // Actions
    // --------------------------------------------------------------------------

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // --------------------------------------------------------------------------
    // Data loaders
    // --------------------------------------------------------------------------

    private function loadYearlyData(Student $student): void
    {
        $counts = Attendance::where('student_id', $student->id)
            ->selectRaw("
                COUNT(*) as working_days,
                SUM(status = 'present') as present,
                SUM(status = 'absent')  as absent,
                SUM(status = 'late')    as late,
                SUM(status = 'excused') as excused
            ")
            ->first();

        $working = (int) ($counts->working_days ?? 0);
        $present = (int) ($counts->present ?? 0);
        $absent = (int) ($counts->absent ?? 0);
        $late = (int) ($counts->late ?? 0);
        $excused = (int) ($counts->excused ?? 0);
        $attended = $present + $late + $excused;
        $pct = $working > 0 ? round(($attended / $working) * 100, 1) : 0.0;

        // SVG ring: r=44, viewBox 0 0 100 100
        $circumference = round(2 * M_PI * 44, 2); // ≈ 276.46
        $dashOffset = round($circumference * (1 - $pct / 100), 2);

        $this->hasRecords = $working > 0;

        $this->yearlyStats = [
            'percentage' => $pct,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'working_days' => $working,
            'circumference' => $circumference,
            'dash_offset' => $dashOffset,
            'ring_color' => $pct >= 75 ? '#22c55e' : ($pct >= 60 ? '#eab308' : '#ef4444'),
            'label' => $pct >= 75 ? 'Good Standing' : ($pct >= 60 ? 'Needs Attention' : 'Critical'),
            'label_bg' => $pct >= 75
                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                : ($pct >= 60
                    ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'
                    : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'),
        ];

        // Month-wise breakdown
        $rows = Attendance::where('student_id', $student->id)
            ->selectRaw("
                YEAR(attendance_date) as yr,
                MONTH(attendance_date) as mo,
                COUNT(*) as working_days,
                SUM(status = 'present') as present,
                SUM(status = 'absent')  as absent,
                SUM(status = 'late')    as late,
                SUM(status = 'excused') as excused
            ")
            ->groupByRaw("YEAR(attendance_date), MONTH(attendance_date)")
            ->orderByRaw("YEAR(attendance_date) ASC, MONTH(attendance_date) ASC")
            ->get();

        $this->monthlyBreakdown = $rows->map(function ($row): array {
            $wd = (int) $row->working_days;
            $p = (int) $row->present;
            $l = (int) $row->late;
            $e = (int) $row->excused;
            $at = $p + $l + $e;
            $rowPct = $wd > 0 ? round(($at / $wd) * 100, 1) : 0.0;

            return [
                'month' => Carbon::createFromDate((int) $row->yr, (int) $row->mo, 1)->format('F Y'),
                'working_days' => $wd,
                'present' => $p,
                'absent' => (int) $row->absent,
                'late' => $l,
                'percentage' => $rowPct,
                'pct_class' => $rowPct >= 75
                    ? 'text-green-600 dark:text-green-400'
                    : ($rowPct >= 60
                        ? 'text-yellow-600 dark:text-yellow-400'
                        : 'text-red-600 dark:text-red-400'),
            ];
        })->toArray();
    }

    private function loadMonthlyData(Student $student): void
    {
        $month = (int) $this->selectedMonth;
        $year = (int) $this->selectedYear;
        $firstDay = Carbon::createFromDate($year, $month, 1);
        $lastDay = $firstDay->copy()->endOfMonth();

        $this->calendarTitle = $firstDay->format('F Y');

        // Attendance records keyed by 'Y-m-d' string
        $records = Attendance::where('student_id', $student->id)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->get(['attendance_date', 'status'])
            ->mapWithKeys(fn ($a) => [
                Carbon::parse($a->attendance_date)->format('Y-m-d') => $a->status,
            ])
            ->toArray();

        // Calendar starts on Monday (ISO 1=Mon … 7=Sun)
        $this->startPadding = $firstDay->dayOfWeekIso - 1;

        $today = now()->toDateString();
        $days = [];

        for ($d = 1; $d <= $lastDay->day; $d++) {
            $date = Carbon::createFromDate($year, $month, $d);
            $dateStr = $date->format('Y-m-d');
            $dow = $date->dayOfWeekIso;

            $days[] = [
                'day' => $d,
                'date' => $dateStr,
                'status' => $records[$dateStr] ?? null,
                'is_weekend' => $dow >= 6,
                'is_future' => $dateStr > $today,
                'is_today' => $dateStr === $today,
            ];
        }

        $this->calendarDays = $days;

        // Stats from marked records only
        $present = count(array_filter($days, fn ($d) => $d['status'] === 'present'));
        $absent = count(array_filter($days, fn ($d) => $d['status'] === 'absent'));
        $late = count(array_filter($days, fn ($d) => $d['status'] === 'late'));
        $excused = count(array_filter($days, fn ($d) => $d['status'] === 'excused'));
        $marked = $present + $absent + $late + $excused;
        $attended = $present + $late + $excused;
        $pct = $marked > 0 ? round(($attended / $marked) * 100, 1) : 0.0;

        $this->monthStats = [
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'working_days' => $marked,
            'percentage' => $pct,
            'label_bg' => $pct >= 75
                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                : ($pct >= 60
                    ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'
                    : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'),
        ];
    }

    // --------------------------------------------------------------------------
    // Access guard
    // --------------------------------------------------------------------------

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && method_exists($user, 'hasRole') && $user->hasRole('student');
    }
}

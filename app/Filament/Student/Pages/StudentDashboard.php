<?php

namespace App\Filament\Student\Pages;

use App\Models\Attendance;
use App\Models\ExamGroup;
use App\Models\ExamScore;
use App\Models\FeeStructure;
use App\Models\GradingLevel;
use App\Models\Notice;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\TimetableSlot;
use App\Models\User;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudentDashboard extends Page
{
    protected string $view = 'filament.student.pages.student-dashboard';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = '';

    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }

    // Data bags — each is a plain array for simple blade rendering
    public string $schoolName = '';

    public array $profile = [];

    public array $attendance = [];

    public array $results = [];

    public array $notices = [];

    public array $feeStatus = [];

    public array $todaySchedule = [];

    public bool $hasProfile = false;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $student = $user?->studentProfile;

        $this->schoolName = SchoolSetting::current()->school_name ?? 'My School';

        if (! $student) {
            return;
        }

        $this->hasProfile = true;
        $student->loadMissing(['user', 'collegeClass', 'academicYear']);

        $this->loadProfile($student);
        $this->loadAttendance($student);
        $this->loadResults($student);
        $this->loadNotices($student);
        $this->loadFeeStatus($student);
        $this->loadTodaySchedule($student);
    }

    private function loadProfile(Student $student): void
    {
        $this->profile = [
            'name' => $student->user?->name ?? '—',
            'class' => $student->collegeClass?->name ?? '—',
            'roll_number' => $student->roll_number ?? '—',
            'admission_no' => $student->admission_number ?? '—',
            'academic_year' => $student->academicYear?->name ?? now()->year.'-'.(now()->year + 1),
            'gender' => $student->gender?->label() ?? '—',
            'status' => $student->status?->label() ?? 'Active',
        ];
    }

    private function loadAttendance(Student $student): void
    {
        $counts = Attendance::where('student_id', $student->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(status = 'present') as present,
                SUM(status = 'absent') as absent,
                SUM(status = 'late') as late,
                SUM(status = 'excused') as excused
            ")
            ->first();

        $total = (int) ($counts->total ?? 0);
        $present = (int) ($counts->present ?? 0);
        $absent = (int) ($counts->absent ?? 0);
        $late = (int) ($counts->late ?? 0);
        $excused = (int) ($counts->excused ?? 0);
        $attended = $present + $late + $excused;
        $pct = $total > 0 ? round(($attended / $total) * 100, 1) : 0.0;

        // SVG ring: circumference for r=44 ≈ 276.46
        $circumference = 2 * M_PI * 44;
        $dashOffset = $circumference * (1 - $pct / 100);

        $this->attendance = [
            'percentage' => $pct,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'total' => $total,
            'circumference' => round($circumference, 2),
            'dash_offset' => round($dashOffset, 2),
            'ring_color' => $pct >= 75 ? '#22c55e' : ($pct >= 60 ? '#eab308' : '#ef4444'),
            'label' => $pct >= 75 ? 'Good' : ($pct >= 60 ? 'Moderate' : 'Low'),
            'label_color' => $pct >= 75 ? 'text-green-600 dark:text-green-400' : ($pct >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400'),
        ];
    }

    private function loadResults(Student $student): void
    {
        $examGroups = ExamGroup::where('college_class_id', $student->college_class_id)
            ->where('is_published', true)
            ->orderByDesc('conducted_date')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        $results = [];

        foreach ($examGroups as $group) {
            $examIds = $group->exams()->pluck('id');

            if ($examIds->isEmpty()) {
                continue;
            }

            $scores = ExamScore::whereIn('exam_id', $examIds)
                ->where('student_id', $student->id)
                ->where('absent', false)
                ->whereNotNull('marks_obtained')
                ->with('exam')
                ->get();

            if ($scores->isEmpty()) {
                continue;
            }

            $obtained = $scores->sum(fn ($s) => (float) $s->marks_obtained);
            $maximum = $scores->sum(fn ($s) => (float) ($s->exam?->maximum_marks ?? 0));
            $pct = $maximum > 0 ? round(($obtained / $maximum) * 100, 1) : 0.0;

            $grade = GradingLevel::calculateGrade($pct, $student->college_class_id)?->name ?? '—';

            $gradeClass = match (true) {
                in_array($grade, ['A1', 'A2']) => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                in_array($grade, ['B1', 'B2']) => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                in_array($grade, ['C1', 'C2']) => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                in_array($grade, ['D1', 'D2']) => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
                $grade === 'E' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
            };

            $pctBarColor = $pct >= 75 ? 'bg-green-500' : ($pct >= 60 ? 'bg-yellow-500' : 'bg-red-500');

            $results[] = [
                'name' => $group->name,
                'type' => $group->type?->shortLabel() ?? '',
                'percentage' => $pct,
                'grade' => $grade,
                'grade_class' => $gradeClass,
                'pct_bar_color' => $pctBarColor,
                'date' => $group->conducted_date?->format('d M Y') ?? '—',
            ];
        }

        $this->results = $results;
    }

    private function loadNotices(Student $student): void
    {
        $notices = Notice::where(function ($q) use ($student): void {
            $q->whereNull('college_class_id')
                ->orWhere('college_class_id', $student->college_class_id);
        })
            ->where(function ($q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('published_at')
            ->take(2)
            ->get();

        $this->notices = $notices->map(fn ($n): array => [
            'title' => $n->title,
            'body' => Str::limit($n->body ?? '', 100),
            'date' => $n->published_at?->format('d M Y') ?? '—',
            'target' => $n->target?->value ?? 'all',
        ])->toArray();
    }

    private function loadFeeStatus(Student $student): void
    {
        $outstanding = $student->getOutstandingAmount();

        $dueDate = FeeStructure::where('college_class_id', $student->college_class_id)
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->value('due_date');

        $isPaid = $outstanding <= 0;
        $isOverdue = $dueDate && ! $isPaid && $dueDate < now()->toDateString();

        $this->feeStatus = [
            'outstanding' => $outstanding,
            'due_date' => $dueDate ? Carbon::parse($dueDate)->format('d M Y') : '—',
            'is_paid' => $isPaid,
            'is_overdue' => $isOverdue,
            'status_label' => $isPaid ? 'Paid' : ($isOverdue ? 'Overdue' : 'Pending'),
            'status_class' => $isPaid
                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                : ($isOverdue
                    ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                    : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'),
        ];
    }

    private function loadTodaySchedule(Student $student): void
    {
        $todayValue = strtolower(now()->format('l')); // 'monday', 'tuesday', …
        $todayFull = now()->format('l'); // 'Monday', 'Tuesday', … (legacy)

        $slots = TimetableSlot::where('college_class_id', $student->college_class_id)
            ->where(function ($q) use ($todayValue, $todayFull): void {
                $q->where('day_of_week', $todayValue)
                    ->orWhere('day', $todayFull)
                    ->orWhere('day', $todayValue);
            })
            ->with(['subject', 'faculty.user'])
            ->orderByRaw('COALESCE(period_number, 999), start_time')
            ->get();

        $now = now();

        $this->todaySchedule = $slots->map(function (TimetableSlot $slot) use ($now): array {
            $startTime = $slot->start_time
                ? Carbon::parse($slot->start_time)->format('h:i A')
                : null;
            $endTime = $slot->end_time
                ? Carbon::parse($slot->end_time)->format('h:i A')
                : null;

            $isCurrent = false;
            if ($slot->start_time && $slot->end_time) {
                $start = Carbon::parse($slot->start_time)->setDate($now->year, $now->month, $now->day);
                $end = Carbon::parse($slot->end_time)->setDate($now->year, $now->month, $now->day);
                $isCurrent = $now->between($start, $end);
            }

            return [
                'period' => $slot->period_number ?? $slot->period ?? '—',
                'subject' => $slot->subject?->name ?? '—',
                'subject_type' => $slot->subject?->subject_type?->value ?? null,
                'faculty' => $slot->faculty?->user?->name ?? '—',
                'start_time' => $startTime,
                'end_time' => $endTime,
                'is_current' => $isCurrent,
            ];
        })->toArray();
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && method_exists($user, 'hasRole') && $user->hasRole('student');
    }
}

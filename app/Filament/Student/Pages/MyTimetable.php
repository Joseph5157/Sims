<?php

namespace App\Filament\Student\Pages;

use App\Enums\DayOfWeek;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\TimetableSlot;
use App\Models\User;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MyTimetable extends Page
{
    protected string $view = 'filament.student.pages.my-timetable';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Timetable';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'My Timetable';

    /** Ordered day sections: [['label','value','is_today','slots'[]], ...] */
    public array $timetable = [];

    /** e.g. 'monday' — used to highlight today's section */
    public string $todayValue = '';

    /** e.g. 'Monday' — used in the banner */
    public string $todayLabel = '';

    public bool $hasProfile = false;

    public bool $hasSlots = false;

    public array $profile = [];

    // --------------------------------------------------------------------------
    // Lifecycle
    // --------------------------------------------------------------------------

    public function mount(): void
    {
        $now = Carbon::now();
        $this->todayValue = strtolower($now->format('l')); // 'monday' … 'sunday'
        $this->todayLabel = $now->format('l');             // 'Monday' … 'Sunday'

        /** @var User $user */
        $user = Auth::user();
        $student = $user?->studentProfile;

        if (! $student) {
            return;
        }

        $this->hasProfile = true;
        $student->loadMissing('collegeClass');

        $this->profile = [
            'name' => $user->name,
            'class' => $student->collegeClass?->name ?? '—',
        ];

        $this->loadTimetable($student);
    }

    // --------------------------------------------------------------------------
    // Data loader
    // --------------------------------------------------------------------------

    private function loadTimetable(Student $student): void
    {
        $query = TimetableSlot::with(['subject', 'faculty.user'])
            ->where('college_class_id', $student->college_class_id);

        // Scope to academic year: prefer student's own year, fall back to current,
        // always include slots that have no year set (legacy / unscoped slots).
        $yearId = $student->academic_year_id
            ?? AcademicYear::where('is_current', true)->value('id');

        if ($yearId) {
            $query->where(function ($q) use ($yearId): void {
                $q->where('academic_year_id', $yearId)
                    ->orWhereNull('academic_year_id');
            });
        }

        $slots = $query->get();

        if ($slots->isEmpty()) {
            $this->timetable = $this->emptyWeek();

            return;
        }

        $this->hasSlots = true;

        // Build day buckets in canonical Mon→Sat order
        $dayOrder = [
            'monday' => 1, 'tuesday' => 2, 'wednesday' => 3,
            'thursday' => 4, 'friday' => 5, 'saturday' => 6,
        ];

        $grouped = [];

        foreach ($slots as $slot) {
            /** @var TimetableSlot $slot */
            $dayEnum = $slot->day_of_week;

            if ($dayEnum instanceof DayOfWeek) {
                $key = $dayEnum->value;
                $label = $dayEnum->label();
                $order = $dayEnum->sortOrder();
            } else {
                // Legacy 'day' string column
                $raw = strtolower((string) ($slot->day ?? 'unknown'));
                $key = $raw;
                $label = ucfirst($raw);
                $order = $dayOrder[$key] ?? 99;
            }

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'label' => $label,
                    'value' => $key,
                    'order' => $order,
                    'is_today' => $key === $this->todayValue,
                    'slots' => [],
                ];
            }

            $grouped[$key]['slots'][] = $this->buildSlotRow($slot);
        }

        // Sort days Mon→Sat
        usort($grouped, fn (array $a, array $b): int => $a['order'] <=> $b['order']);

        // Sort slots within each day by period_number
        foreach ($grouped as &$day) {
            usort($day['slots'], fn (array $a, array $b): int => $a['sort_key'] <=> $b['sort_key']);
        }
        unset($day);

        // Merge into full Mon–Sat week (days with no slots show empty state)
        $fullWeek = $this->emptyWeek();

        foreach ($grouped as $dayData) {
            foreach ($fullWeek as &$weekDay) {
                if ($weekDay['value'] === $dayData['value']) {
                    $weekDay['slots'] = $dayData['slots'];
                    break;
                }
            }
            unset($weekDay);
        }

        $this->timetable = $fullWeek;
    }

    /** Build a flat display array from a single TimetableSlot model. */
    private function buildSlotRow(TimetableSlot $slot): array
    {
        $startTime = $slot->start_time
            ? Carbon::parse($slot->start_time)->format('h:i A')
            : null;
        $endTime = $slot->end_time
            ? Carbon::parse($slot->end_time)->format('h:i A')
            : null;

        $subjectType = $slot->subject?->subject_type?->value; // 'theory','practical','elective','project'
        $periodNumber = $slot->period_number ?? (int) ($slot->period ?? 0);

        return [
            'sort_key' => $periodNumber ?: 999,
            'period' => $periodNumber ?: '—',
            'start_time' => $startTime,
            'end_time' => $endTime,
            'subject' => $slot->subject?->name ?? '—',
            'subject_type' => $subjectType,
            'subject_badge' => $this->subjectTypeBadge($subjectType),
            'subject_label' => $slot->subject?->subject_type?->label() ?? null,
            'faculty' => $slot->faculty?->user?->name ?? null,
            'room' => $slot->room ?? null,
        ];
    }

    /** Return a full Mon–Sat skeleton with empty slot arrays. */
    private function emptyWeek(): array
    {
        $days = [
            ['monday', 'Monday', 1],
            ['tuesday', 'Tuesday', 2],
            ['wednesday', 'Wednesday', 3],
            ['thursday', 'Thursday', 4],
            ['friday', 'Friday', 5],
            ['saturday', 'Saturday', 6],
        ];

        return array_map(fn (array $d): array => [
            'value' => $d[0],
            'label' => $d[1],
            'order' => $d[2],
            'is_today' => $d[0] === $this->todayValue,
            'slots' => [],
        ], $days);
    }

    /** Tailwind badge classes for each subject type. */
    private function subjectTypeBadge(?string $type): string
    {
        return match ($type) {
            'theory' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'practical' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
            'elective' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
            'project' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
            default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
        };
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

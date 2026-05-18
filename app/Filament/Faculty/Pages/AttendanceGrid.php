<?php

namespace App\Filament\Faculty\Pages;

use App\Models\Attendance;
use App\Models\CollegeClass;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\TimetableSlot;
use App\Models\User;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceGrid extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected string $view = 'filament.faculty.pages.attendance-grid';

    protected static ?string $navigationLabel = 'Monthly Grid';

    protected static ?string $slug = 'attendance-grid';

    protected static ?int $navigationSort = 3;

    public ?int $selectedClassId = null;

    public int $month;

    public int $year;

    /** @var array<int, string> [id => name] */
    public array $facultyClasses = [];

    /**
     * @var array<int, array{
     *   id: int, roll_number: string, name: string,
     *   percentage: float, present: int, absent: int, late: int, excused: int
     * }>
     */
    public array $students = [];

    /** @var array<int, array<int, string>> Persisted statuses keyed by student and day. */
    public array $gridData = [];

    /** @var array<int, array<int, string>> Draft-only statuses keyed by student and day. */
    public array $draftChanges = [];

    public int $daysInMonth = 0;

    /** @var array<int, int> [day => present+late+excused count] */
    public array $dayTotals = [];

    public ?int $activeStudentId = null;

    public ?int $activeDay = null;

    public bool $hasUnsavedChanges = false;

    public function mount(): void
    {
        $this->month = (int) now()->month;
        $this->year = (int) now()->year;
        $this->loadFacultyClasses();

        if (count($this->facultyClasses) === 1) {
            $this->selectedClassId = (int) array_key_first($this->facultyClasses);
            $this->loadGrid();
        }
    }

    public function updatedSelectedClassId(): void
    {
        $this->loadGrid();
    }

    public function updatedMonth(): void
    {
        $this->loadGrid();
    }

    public function updatedYear(): void
    {
        $this->loadGrid();
    }

    private function loadFacultyClasses(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $faculty = Faculty::where('user_id', $user->id)->first();

        if (! $faculty) {
            $this->facultyClasses = [];

            return;
        }

        $classIds = TimetableSlot::where('faculty_id', $faculty->id)
            ->pluck('college_class_id')
            ->unique()
            ->values();

        $this->facultyClasses = CollegeClass::whereIn('id', $classIds)
            ->pluck('name', 'id')
            ->toArray();

        if (empty($this->facultyClasses) && $faculty->department_id) {
            $this->facultyClasses = CollegeClass::where('department_id', $faculty->department_id)
                ->pluck('name', 'id')
                ->toArray();
        }
    }

    public function loadGrid(): void
    {
        $this->resetDraftState();

        if (! $this->selectedClassId) {
            $this->students = [];
            $this->gridData = [];
            $this->daysInMonth = 0;
            $this->dayTotals = [];

            return;
        }

        $this->daysInMonth = Carbon::createFromDate($this->year, $this->month, 1)->daysInMonth;

        $studentModels = Student::where('college_class_id', $this->selectedClassId)
            ->with('user')
            ->orderBy('roll_number')
            ->get();

        $startDate = sprintf('%04d-%02d-01', $this->year, $this->month);
        $endDate = sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->daysInMonth);

        $records = Attendance::where('college_class_id', $this->selectedClassId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        $indexed = [];
        foreach ($records as $record) {
            $day = (int) Carbon::parse($record->attendance_date)->day;
            $indexed[$record->student_id][$day] = $record->status;
        }

        $this->students = $studentModels->map(fn (Student $student): array => [
            'id' => $student->id,
            'roll_number' => $student->roll_number,
            'name' => $student->user?->name ?? '—',
            'percentage' => 0.0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
        ])->toArray();

        $this->gridData = $indexed;
        $this->refreshDerivedState();
    }

    public function selectCell(int $studentId, int $day): void
    {
        if (! $this->selectedClassId) {
            return;
        }

        $this->activeStudentId = $studentId;
        $this->activeDay = $day;
    }

    public function applyStatus(string $status): void
    {
        if (! in_array($status, ['present', 'absent', 'late', 'excused'], true)) {
            return;
        }

        if ($this->activeStudentId === null || $this->activeDay === null) {
            return;
        }

        $studentId = $this->activeStudentId;
        $day = $this->activeDay;
        $persistedStatus = $this->gridData[$studentId][$day] ?? null;

        if ($persistedStatus === $status) {
            if (isset($this->draftChanges[$studentId])) {
                unset($this->draftChanges[$studentId][$day]);
                if ($this->draftChanges[$studentId] === []) {
                    unset($this->draftChanges[$studentId]);
                }
            }
        } else {
            $this->draftChanges[$studentId][$day] = $status;
        }

        $this->syncDraftFlags();
        $this->refreshDerivedState();
    }

    public function discardChanges(): void
    {
        $this->resetDraftState();
        $this->refreshDerivedState();
    }

    public function saveAll(): void
    {
        if (! $this->selectedClassId || ! $this->hasUnsavedChanges) {
            return;
        }

        $savedCount = 0;
        $userId = Auth::id();

        foreach ($this->draftChanges as $studentId => $days) {
            foreach ($days as $day => $status) {
                $date = sprintf('%04d-%02d-%02d', $this->year, $this->month, $day);

                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'college_class_id' => $this->selectedClassId,
                        'attendance_date' => $date,
                    ],
                    [
                        'status' => $status,
                        'marked_by' => $userId,
                    ]
                );

                $this->gridData[$studentId][$day] = $status;
                $savedCount++;
            }
        }

        $this->resetDraftState();
        $this->refreshDerivedState();

        Notification::make()
            ->title('Attendance changes saved')
            ->body("Saved {$savedCount} attendance update(s).")
            ->success()
            ->send();
    }

    public function getMonthName(): string
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
    }

    public function getActiveStudent(): ?array
    {
        if ($this->activeStudentId === null) {
            return null;
        }

        foreach ($this->students as $student) {
            if ($student['id'] === $this->activeStudentId) {
                return $student;
            }
        }

        return null;
    }

    public function getActiveCellDateLabel(): ?string
    {
        if ($this->activeDay === null) {
            return null;
        }

        return Carbon::createFromDate($this->year, $this->month, $this->activeDay)->format('d M Y');
    }

    public function getActiveCellStatus(): ?string
    {
        if ($this->activeStudentId === null || $this->activeDay === null) {
            return null;
        }

        return $this->getDisplayStatus($this->activeStudentId, $this->activeDay);
    }

    public function getDisplayStatus(int $studentId, int $day): ?string
    {
        if (isset($this->draftChanges[$studentId]) && array_key_exists($day, $this->draftChanges[$studentId])) {
            return $this->draftChanges[$studentId][$day];
        }

        return $this->gridData[$studentId][$day] ?? null;
    }

    public function isDraftCell(int $studentId, int $day): bool
    {
        return isset($this->draftChanges[$studentId]) && array_key_exists($day, $this->draftChanges[$studentId]);
    }

    public function getStatusLabel(?string $status): string
    {
        return match ($status) {
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'excused' => 'Excused',
            default => 'Not marked',
        };
    }

    public function getStatusIcon(?string $status): string
    {
        return match ($status) {
            'present' => 'heroicon-o-check-circle',
            'absent' => 'heroicon-o-x-circle',
            'late' => 'heroicon-o-clock',
            'excused' => 'heroicon-o-shield-check',
            default => 'heroicon-o-minus-circle',
        };
    }

    public function getCellAriaLabel(array $student, int $day, ?string $status, bool $isSelected, bool $isDraft): string
    {
        $dateLabel = Carbon::createFromDate($this->year, $this->month, $day)->format('j F Y');
        $parts = [
            $student['name'],
            $dateLabel,
            'Status: '.$this->getStatusLabel($status),
        ];

        if ($isSelected) {
            $parts[] = 'Selected';
        }

        if ($isDraft) {
            $parts[] = 'Draft change pending';
        }

        return implode('. ', $parts).'.';
    }

    private function resetDraftState(): void
    {
        $this->draftChanges = [];
        $this->activeStudentId = null;
        $this->activeDay = null;
        $this->hasUnsavedChanges = false;
    }

    private function syncDraftFlags(): void
    {
        foreach ($this->draftChanges as $studentId => $days) {
            if ($days === []) {
                unset($this->draftChanges[$studentId]);
            }
        }

        $this->hasUnsavedChanges = $this->draftChanges !== [];
    }

    private function refreshDerivedState(): void
    {
        $this->dayTotals = $this->daysInMonth > 0
            ? array_fill(1, $this->daysInMonth, 0)
            : [];

        foreach ($this->students as &$student) {
            $present = 0;
            $absent = 0;
            $late = 0;
            $excused = 0;

            for ($day = 1; $day <= $this->daysInMonth; $day++) {
                $status = $this->getDisplayStatus($student['id'], $day);

                match ($status) {
                    'present' => [$present++, $this->dayTotals[$day]++],
                    'absent' => $absent++,
                    'late' => [$late++, $this->dayTotals[$day]++],
                    'excused' => [$excused++, $this->dayTotals[$day]++],
                    default => null,
                };
            }

            $totalMarked = $present + $absent + $late + $excused;
            $student['present'] = $present;
            $student['absent'] = $absent;
            $student['late'] = $late;
            $student['excused'] = $excused;
            $student['percentage'] = $totalMarked > 0
                ? round((($present + $late + $excused) / $totalMarked) * 100, 1)
                : 0.0;
        }
        unset($student);
    }
}

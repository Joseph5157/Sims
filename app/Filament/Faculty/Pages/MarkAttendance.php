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
use Illuminate\Support\Facades\Auth;

class MarkAttendance extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected string $view = 'filament.faculty.pages.mark-attendance';

    protected static ?string $navigationLabel = 'Mark Attendance';

    protected static ?int $navigationSort = 1;

    /** Selected class ID */
    public ?int $selectedClassId = null;

    /** Attendance date - defaults to today */
    public string $attendanceDate = '';

    /** Reason shown when backdating */
    public string $editReason = '';

    /** [student_id => status] - status: present|absent|late|excused */
    public array $attendance = [];

    /** [id => name] classes this faculty teaches */
    public array $facultyClasses = [];

    /** [['id', 'roll_number', 'name'], ...] */
    public array $students = [];

    /** True when at least one record already existed for date+class */
    public bool $alreadyMarked = false;

    public function mount(): void
    {
        $this->attendanceDate = now()->toDateString();
        $this->loadFacultyClasses();

        if (count($this->facultyClasses) === 1) {
            $this->selectedClassId = (int) array_key_first($this->facultyClasses);
            $this->loadStudents();
        }
    }

    public function updatedSelectedClassId(): void
    {
        $this->loadStudents();
    }

    public function updatedAttendanceDate(): void
    {
        if ($this->attendanceDate === now()->toDateString()) {
            $this->editReason = '';
        }

        $this->loadStudents();
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

    public function loadStudents(): void
    {
        if (! $this->selectedClassId) {
            $this->students = [];
            $this->attendance = [];
            $this->alreadyMarked = false;

            return;
        }

        $this->students = Student::where('college_class_id', $this->selectedClassId)
            ->with('user')
            ->orderBy('roll_number')
            ->get()
            ->map(fn (Student $s): array => [
                'id' => $s->id,
                'roll_number' => $s->roll_number,
                'name' => $s->user?->name ?? '—',
            ])
            ->toArray();

        $existing = Attendance::where('college_class_id', $this->selectedClassId)
            ->where('attendance_date', $this->attendanceDate)
            ->pluck('status', 'student_id')
            ->toArray();

        $this->alreadyMarked = ! empty($existing);

        $this->attendance = [];
        foreach ($this->students as $student) {
            $this->attendance[$student['id']] = $existing[$student['id']] ?? 'present';
        }
    }

    public function setStatus(int $studentId, string $status): void
    {
        if (in_array($status, ['present', 'absent', 'late', 'excused'], true)) {
            $this->attendance[$studentId] = $status;
        }
    }

    public function markAllPresent(): void
    {
        foreach ($this->students as $student) {
            $this->attendance[$student['id']] = 'present';
        }
    }

    public function markAllAbsent(): void
    {
        foreach ($this->students as $student) {
            $this->attendance[$student['id']] = 'absent';
        }
    }

    public function submit(): void
    {
        if (! $this->selectedClassId || empty($this->students)) {
            Notification::make()->title('Select a class first')->warning()->send();

            return;
        }

        $isBackdated = $this->attendanceDate !== now()->toDateString();

        if ($isBackdated && empty(trim($this->editReason))) {
            Notification::make()
                ->title('Edit reason required for backdated attendance')
                ->warning()
                ->send();

            return;
        }

        $userId = Auth::id();
        $counts = ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];

        foreach ($this->attendance as $studentId => $status) {
            $payload = [
                'status' => $status,
                'marked_by' => $userId,
            ];

            if ($isBackdated) {
                $payload['edit_reason'] = trim($this->editReason);
            }

            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'college_class_id' => $this->selectedClassId,
                    'attendance_date' => $this->attendanceDate,
                ],
                $payload
            );

            if (array_key_exists($status, $counts)) {
                $counts[$status]++;
            }
        }

        $className = $this->facultyClasses[$this->selectedClassId] ?? 'Class';

        Notification::make()
            ->title("Attendance saved - {$className}")
            ->body(
                "Present: {$counts['present']}  Absent: {$counts['absent']}  Late: {$counts['late']}  Excused: {$counts['excused']}"
            )
            ->success()
            ->send();

        $this->alreadyMarked = true;
    }

    public function getPresentCount(): int
    {
        return count(array_filter($this->attendance, fn (string $s): bool => $s === 'present'));
    }

    public function getAbsentCount(): int
    {
        return count(array_filter($this->attendance, fn (string $s): bool => $s === 'absent'));
    }

    public function getLateCount(): int
    {
        return count(array_filter($this->attendance, fn (string $s): bool => $s === 'late'));
    }

    public function getExcusedCount(): int
    {
        return count(array_filter($this->attendance, fn (string $s): bool => $s === 'excused'));
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
}

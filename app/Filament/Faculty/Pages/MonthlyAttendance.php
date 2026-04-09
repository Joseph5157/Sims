<?php

namespace App\Filament\Faculty\Pages;

use App\Models\Attendance;
use App\Models\CollegeClass;
use App\Models\Student;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class MonthlyAttendance extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Monthly Attendance';
    protected static string|UnitEnum|null $navigationGroup = 'Academic';
    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.faculty.pages.monthly-attendance';

    public ?int $college_class_id = null;
    public string $month = '';
    public array $classOptions = [];
    public array $students = [];
    public array $days = [];
    public array $attendance = [];

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
        $this->classOptions = CollegeClass::orderBy('name')->pluck('name', 'id')->toArray();

        if (blank($this->college_class_id) && count($this->classOptions) > 0) {
            $this->college_class_id = (int) array_key_first($this->classOptions);
        }

        $this->buildGrid();
    }

    public function updatedCollegeClassId(): void
    {
        $this->buildGrid();
    }

    public function updatedMonth(): void
    {
        $this->buildGrid();
    }

    public function selectMonth(string $month): void
    {
        $this->month = $month;
        $this->buildGrid();
    }

    public function toggleAttendance(int $studentId, string $date): void
    {
        if (! $this->college_class_id) {
            return;
        }

        $selectedDate = Carbon::parse($date)->startOfDay();
        if ($selectedDate->isAfter(now()->startOfDay())) {
            Notification::make()
                ->title('Future dates are locked')
                ->body('You can only mark attendance for today or past dates.')
                ->warning()
                ->send();
            return;
        }

        $existing = Attendance::where('student_id', $studentId)
            ->where('college_class_id', $this->college_class_id)
            ->whereDate('attendance_date', $date)
            ->first();

        $nextStatus = ($existing?->status ?? 'absent') === 'present' ? 'absent' : 'present';

        Attendance::updateOrCreate(
            [
                'student_id' => $studentId,
                'college_class_id' => $this->college_class_id,
                'attendance_date' => $date,
            ],
            [
                'status' => $nextStatus,
                'marked_by' => Auth::id(),
                'notes' => $existing?->notes,
            ]
        );

        $this->attendance[$studentId][$date] = $nextStatus;
    }

    public function getMonthTabs(): array
    {
        $base = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();

        return [
            $base->copy()->subMonth()->format('Y-m') => $base->copy()->subMonth()->format('M Y'),
            $base->format('Y-m') => $base->format('M Y'),
            $base->copy()->addMonth()->format('Y-m') => $base->copy()->addMonth()->format('M Y'),
        ];
    }

    private function buildGrid(): void
    {
        if (! $this->college_class_id) {
            $this->students = [];
            $this->days = [];
            $this->attendance = [];
            return;
        }

        $start = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $this->days = [];
        for ($i = 0; $i < $start->daysInMonth; $i++) {
            $date = $start->copy()->addDays($i);
            $this->days[] = [
                'date' => $date->toDateString(),
                'label' => $date->format('d'),
            ];
        }

        $studentRecords = Student::where('college_class_id', $this->college_class_id)
            ->with('user')
            ->orderBy('roll_number')
            ->get();

        $this->students = $studentRecords->map(function (Student $student): array {
            return [
                'id' => $student->id,
                'roll_number' => $student->roll_number,
                'name' => $student->user?->name ?? 'Unknown',
            ];
        })->toArray();

        $this->attendance = [];
        $attendanceRecords = Attendance::where('college_class_id', $this->college_class_id)
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->get(['student_id', 'attendance_date', 'status']);

        foreach ($attendanceRecords as $record) {
            $dateKey = $record->attendance_date instanceof Carbon
                ? $record->attendance_date->toDateString()
                : Carbon::parse($record->attendance_date)->toDateString();

            $this->attendance[$record->student_id][$dateKey] = $record->status;
        }
    }
}

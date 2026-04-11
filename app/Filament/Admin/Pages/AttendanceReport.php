<?php

namespace App\Filament\Admin\Pages;

use App\Models\Attendance;
use App\Models\CollegeClass;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\TimetableSlot;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class AttendanceReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.admin.pages.attendance-report';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Attendance Report';

    protected static ?int $navigationSort = 10;

    protected static bool $shouldRegisterNavigation = true;

    /**
     * @var array{college_class_id: int|null, month: int|null, year: int|null}
     */
    public array $data = [
        'college_class_id' => null,
        'month' => null,
        'year' => null,
    ];

    public Collection $students;

    /**
     * @var array<int>
     */
    public array $days = [];

    /**
     * @var array<int, array{student: Student, cells: array<int, string>, present: int, absent: int, percentage: float}>
     */
    public array $rows = [];

    public function mount(): void
    {
        $this->students = collect();
        $this->days = [];
        $this->rows = [];

        $now = now();
        $this->data['month'] = (int) $now->month;
        $this->data['year'] = (int) $now->year;

        $this->form->fill($this->data);
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Attendance Report';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Month-wise attendance grid';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Select::make('college_class_id')
                    ->label('Class')
                    ->options(fn (): array => $this->getCollegeClassOptions())
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (): void {
                        $this->loadReport();
                    }),

                Select::make('month')
                    ->label('Month')
                    ->options($this->getMonthOptions())
                    ->required()
                    ->default((int) now()->month)
                    ->live()
                    ->afterStateUpdated(function (): void {
                        $this->loadReport();
                    }),

                TextInput::make('year')
                    ->label('Year')
                    ->numeric()
                    ->required()
                    ->default((int) now()->year)
                    ->live()
                    ->afterStateUpdated(function (): void {
                        $this->loadReport();
                    }),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn (): StreamedResponse => $this->exportCsv())
                ->disabled(fn (): bool => empty($this->rows)),
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function getMonthOptions(): array
    {
        return [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function getCollegeClassOptions(): array
    {
        $query = CollegeClass::query()->orderBy('name');

        $allowed = $this->getAllowedCollegeClassIdsForCurrentUser();
        if ($allowed !== null) {
            if ($allowed === []) {
                return [];
            }

            $query->whereIn('id', $allowed);
        }

        return $query->pluck('name', 'id')->all();
    }

    /**
     * @return array<int>|null
     */
    protected function getAllowedCollegeClassIdsForCurrentUser(): ?array
    {
        $panelId = Filament::getCurrentOrDefaultPanel()?->getId();

        if ($panelId !== 'faculty') {
            return null;
        }

        $userId = Auth::id();
        if (! $userId) {
            return [];
        }

        $faculty = Faculty::query()->where('user_id', $userId)->first();
        if (! $faculty) {
            return [];
        }

        return TimetableSlot::query()
            ->where('faculty_id', $faculty->id)
            ->distinct()
            ->pluck('college_class_id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    public function loadReport(): void
    {
        $classId = (int) ($this->data['college_class_id'] ?? 0);
        $month = (int) ($this->data['month'] ?? 0);
        $year = (int) ($this->data['year'] ?? 0);

        $this->students = collect();
        $this->days = [];
        $this->rows = [];

        if ($classId <= 0 || $month <= 0 || $year <= 0) {
            return;
        }

        $allowed = $this->getAllowedCollegeClassIdsForCurrentUser();
        if ($allowed !== null && ! in_array($classId, $allowed, true)) {
            // Faculty panel safety: if user tries to set an arbitrary class id.
            $this->data['college_class_id'] = null;

            return;
        }

        $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $end = (clone $start)->endOfMonth()->endOfDay();

        $daysInMonth = (int) $start->daysInMonth;
        $this->days = range(1, $daysInMonth);

        $students = Student::query()
            ->with('user')
            ->where('college_class_id', $classId)
            ->orderBy('roll_number')
            ->get();

        $this->students = $students;

        $attendanceRows = Attendance::query()
            ->where('college_class_id', $classId)
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->get(['student_id', 'attendance_date', 'status']);

        /** @var array<int, array<int, string>> $attendanceMap */
        $attendanceMap = [];

        foreach ($attendanceRows as $attendance) {
            $day = (int) Carbon::parse($attendance->attendance_date)->day;
            $attendanceMap[(int) $attendance->student_id][$day] = (string) $attendance->status;
        }

        $rows = [];

        foreach ($students as $student) {
            $present = 0;
            $absent = 0;
            $cells = [];

            foreach ($this->days as $day) {
                $status = $attendanceMap[(int) $student->id][$day] ?? null;

                if ($status === null) {
                    $cells[$day] = '-';

                    continue;
                }

                if ($status === 'absent') {
                    $cells[$day] = 'A';
                    $absent++;

                    continue;
                }

                // Treat present/late/excused as present.
                $cells[$day] = 'P';
                $present++;
            }

            $totalMarked = $present + $absent;
            $percentage = $totalMarked > 0 ? round(($present / $totalMarked) * 100, 1) : 0.0;

            $rows[] = [
                'student' => $student,
                'cells' => $cells,
                'present' => $present,
                'absent' => $absent,
                'percentage' => $percentage,
            ];
        }

        $this->rows = $rows;
    }

    public function exportCsv(): StreamedResponse
    {
        $classId = (int) ($this->data['college_class_id'] ?? 0);
        $month = (int) ($this->data['month'] ?? 0);
        $year = (int) ($this->data['year'] ?? 0);

        $filename = 'attendance-report-'.now()->format('Y-m-d').'.csv';

        if ($classId <= 0 || $month <= 0 || $year <= 0 || empty($this->rows)) {
            return response()->streamDownload(fn () => null, $filename);
        }

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            $header = ['Student', 'Roll No'];
            foreach ($this->days as $day) {
                $header[] = (string) $day;
            }
            $header[] = 'Total Present';
            $header[] = 'Total Absent';
            $header[] = 'Percentage';

            fputcsv($handle, $header);

            foreach ($this->rows as $row) {
                /** @var Student $student */
                $student = $row['student'];
                /** @var array<int, string> $cells */
                $cells = $row['cells'];

                $csvRow = [
                    $student->user?->name ?? $student->roll_number,
                    $student->roll_number,
                ];

                foreach ($this->days as $day) {
                    $csvRow[] = $cells[$day] ?? '-';
                }

                $csvRow[] = (string) $row['present'];
                $csvRow[] = (string) $row['absent'];
                $csvRow[] = $row['percentage'].'%';

                fputcsv($handle, $csvRow);
            }

            fclose($handle);
        }, $filename);
    }
}

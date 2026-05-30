<?php

namespace App\Filament\Admin\Pages;

use App\Mail\AttendanceWarningMail;
use App\Models\AcademicYear;
use App\Models\CollegeClass;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use UnitEnum;

class AttendanceDefaulters extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.admin.pages.attendance-defaulters';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|UnitEnum|null $navigationGroup = 'Attendance';

    protected static ?string $navigationLabel = 'Attendance Defaulters';

    protected static ?int $navigationSort = 2;

    /** @var array<string, mixed> */
    public array $filters = [
        'college_class_id' => null,
        'academic_year_id' => null,
    ];

    public Collection $defaulters;

    public function mount(): void
    {
        $this->defaulters = collect();
        $this->form->fill($this->filters);
        $this->loadDefaulters();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('filters')
            ->schema([
                Select::make('college_class_id')
                    ->label('Filter by Class')
                    ->options(fn (): array => CollegeClass::orderBy('name')->pluck('name', 'id')->all())
                    ->placeholder('All Classes')
                    ->nullable()
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadDefaulters()),

                Select::make('academic_year_id')
                    ->label('Filter by Academic Year')
                    ->options(fn (): array => AcademicYear::orderByDesc('start_year')->pluck('name', 'id')->all())
                    ->placeholder('All Years')
                    ->nullable()
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadDefaulters()),
            ]);
    }

    protected function loadDefaulters(): void
    {
        $classId = $this->filters['college_class_id'] ?? null;
        $yearId = $this->filters['academic_year_id'] ?? null;

        // Aggregate attendance per student using confirmed column names:
        // status values: present, absent, late, excused
        // present_count = present + late + excused; absent_count = absent only
        $attendanceQuery = DB::table('attendances')
            ->selectRaw("
                student_id,
                SUM(CASE WHEN status IN ('present', 'late', 'excused') THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                COUNT(*) as total_count
            ")
            ->groupBy('student_id')
            ->havingRaw('total_count > 0 AND (CAST(present_count AS REAL) / total_count * 100) < 75');

        if ($classId) {
            $attendanceQuery->where('college_class_id', $classId);
        }

        $stats = $attendanceQuery->get()->keyBy('student_id');

        $studentQuery = Student::query()
            ->with(['user', 'collegeClass', 'guardians'])
            ->whereIn('id', $stats->keys())
            ->orderBy('roll_number');

        if ($classId) {
            $studentQuery->where('college_class_id', $classId);
        }

        if ($yearId) {
            $studentQuery->where('academic_year_id', $yearId);
        }

        $this->defaulters = $studentQuery
            ->get()
            ->map(function (Student $student) use ($stats): Student {
                /** @var object{present_count: mixed, absent_count: mixed, total_count: mixed} $s */
                $s = $stats[$student->id];
                $present = (int) $s->present_count;
                $absent = (int) $s->absent_count;
                $total = (int) $s->total_count;

                $student->present_count = $present;
                $student->absent_count = $absent;
                $student->total_count = $total;
                $student->attendance_percentage = $total > 0
                    ? round($present / $total * 100, 1)
                    : 0.0;

                // Days of consecutive 100% attendance needed to reach 75%
                // Formula: ceil(3 × absent − present)
                $student->shortfall = max(0, (int) ceil(3 * $absent - $present));

                return $student;
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('notifyAll')
                ->label('Notify All Parents')
                ->icon('heroicon-o-envelope')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Send Warning Emails to All Parents')
                ->modalDescription('This will email all guardians (with a registered email) of students below 75% attendance. Continue?')
                ->action(function (): void {
                    $sent = 0;
                    $skipped = 0;

                    foreach ($this->defaulters as $student) {
                        $guardian = $student->guardians->firstWhere('is_primary_contact', true)
                            ?? $student->guardians->first();

                        if ($guardian && $guardian->email) {
                            Mail::to($guardian->email)
                                ->send(new AttendanceWarningMail($student, $guardian));
                            $sent++;
                        } else {
                            $skipped++;
                        }
                    }

                    $message = "Sent {$sent} email(s).";
                    if ($skipped > 0) {
                        $message .= " Skipped {$skipped} (no guardian email on record).";
                    }

                    Notification::make()->title($message)->success()->send();
                }),
        ];
    }

    /**
     * Called via wire:click from the Blade view to notify a single student's guardian.
     */
    public function notifyParent(int $studentId): void
    {
        $student = $this->defaulters->firstWhere('id', $studentId);

        if (! $student) {
            return;
        }

        $guardian = $student->guardians->firstWhere('is_primary_contact', true)
            ?? $student->guardians->first();

        if (! $guardian || ! $guardian->email) {
            Notification::make()
                ->title('No guardian email found for this student.')
                ->warning()
                ->send();

            return;
        }

        Mail::to($guardian->email)
            ->send(new AttendanceWarningMail($student, $guardian));

        Notification::make()
            ->title('Warning email sent to '.$guardian->fullName().' ('.$guardian->email.')')
            ->success()
            ->send();
    }

    public function getHeading(): string
    {
        return 'Attendance Defaulters';
    }

    public function getSubheading(): string
    {
        $count = $this->defaulters->count();

        return "{$count} student(s) below 75% attendance";
    }
}

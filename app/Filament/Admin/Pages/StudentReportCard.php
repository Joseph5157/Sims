<?php

namespace App\Filament\Admin\Pages;

use App\Models\ExamGroup;
use App\Models\ExamScore;
use App\Models\GradingLevel;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use UnitEnum;

class StudentReportCard extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.admin.pages.student-report-card';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Student Report Card';

    protected static ?int $navigationSort = 20;

    public ?array $data = [
        'student_id' => null,
        'exam_group_id' => null,
    ];

    public Collection $subjects;

    public function mount(): void
    {
        $this->subjects = collect();
        $this->form->fill($this->data);
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Student Report Card';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Printable academic summary';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Select::make('student_id')
                    ->label('Student')
                    ->options(Student::query()
                        ->with('user')
                        ->orderBy('roll_number')
                        ->get()
                        ->mapWithKeys(fn (Student $record): array => [
                            $record->id => ($record->user?->name ?? 'Student').' - '.$record->roll_number,
                        ])
                        ->all())
                    ->getOptionLabelFromRecordUsing(fn (Student $record): string => ($record->user?->name ?? 'Student').' - '.$record->roll_number)
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (): void {
                        $this->data['exam_group_id'] = null;
                    }),

                Select::make('exam_group_id')
                    ->label('Exam Group')
                    ->options(fn (): array => $this->getExamGroupOptions())
                    ->searchable()
                    ->required()
                    ->live(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->alpineClickHandler('window.print();'),
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function getExamGroupOptions(): array
    {
        $studentId = (int) ($this->data['student_id'] ?? 0);

        if ($studentId <= 0) {
            return [];
        }

        $student = Student::with('collegeClass')->find($studentId);

        if (! $student?->college_class_id) {
            return [];
        }

        return ExamGroup::query()
            ->where('college_class_id', $student->college_class_id)
            ->where('is_published', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $studentId = (int) ($this->data['student_id'] ?? 0);
        $examGroupId = (int) ($this->data['exam_group_id'] ?? 0);

        $student = Student::with(['user', 'collegeClass', 'department'])->find($studentId);
        $examGroup = ExamGroup::with(['collegeClass.department', 'exams.subject'])->find($examGroupId);

        if (! $student || ! $examGroup || (int) $student->college_class_id !== (int) $examGroup->college_class_id) {
            return [
                'studentName' => null,
                'rollNumber' => null,
                'className' => null,
                'departmentName' => null,
                'attendancePercentage' => null,
                'marks' => collect(),
                'totalObtained' => 0.0,
                'totalMax' => 0.0,
                'overallPercentage' => 0.0,
                'overallGrade' => null,
            ];
        }

        $subjects = $examGroup->exams->map(fn ($exam) => $exam->subject)->filter()->values();

        $examScores = ExamScore::whereHas('exam', fn ($query) => $query->where('exam_group_id', $examGroup->id))
            ->where('student_id', $student->id)
            ->with('exam.subject')
            ->get()
            ->keyBy(fn ($score): int => (int) $score->exam->subject_id);

        $marks = collect();
        $totalObtained = 0.0;
        $totalMax = 0.0;

        foreach ($subjects as $subject) {
            $score = $examScores->get($subject->id);
            $exam = $examGroup->exams->firstWhere('subject_id', $subject->id);
            $maxMarks = (float) ($exam?->maximum_marks ?? 0);
            $obtained = (float) ($score?->marks_obtained ?? 0);
            $percentage = $maxMarks > 0 ? round(($obtained / $maxMarks) * 100, 1) : 0.0;
            $grade = GradingLevel::calculateGrade($percentage, $student->college_class_id);

            $marks->push([
                'subject' => $subject,
                'max_marks' => $maxMarks,
                'obtained' => $obtained,
                'percentage' => $percentage,
                'grade' => $grade?->name ?? '-',
            ]);

            $totalObtained += $obtained;
            $totalMax += $maxMarks;
        }

        $overallPercentage = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 1) : 0.0;
        $overallGrade = GradingLevel::calculateGrade($overallPercentage, $student->college_class_id)?->name;

        return [
            'studentName' => $student->user?->name,
            'rollNumber' => $student->roll_number,
            'className' => $student->collegeClass?->name,
            'departmentName' => $student->department?->name,
            'attendancePercentage' => $student->getAttendancePercentage(),
            'marks' => $marks,
            'totalObtained' => $totalObtained,
            'totalMax' => $totalMax,
            'overallPercentage' => $overallPercentage,
            'overallGrade' => $overallGrade,
        ];
    }
}

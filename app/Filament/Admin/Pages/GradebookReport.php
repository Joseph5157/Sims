<?php

namespace App\Filament\Admin\Pages;

use App\Models\CollegeClass;
use App\Models\Exam;
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
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class GradebookReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.admin.pages.gradebook-report';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static string|UnitEnum|null $navigationGroup = 'Examinations';

    protected static ?string $navigationLabel = 'Gradebook Report';

    protected static ?int $navigationSort = 10;

    public ?int $college_class_id = null;

    public ?int $exam_group_id = null;

    public Collection $students;

    public Collection $exams;

    public array $scores = [];

    public function mount(): void
    {
        $this->students = collect();
        $this->exams = collect();
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('college_class_id')
                ->label('Select Class')
                ->options(CollegeClass::pluck('name', 'id'))
                ->live()
                ->afterStateUpdated(function (): void {
                    $this->exam_group_id = null;
                    $this->students = collect();
                    $this->exams = collect();
                    $this->scores = [];
                }),
            Select::make('exam_group_id')
                ->label('Select Exam Group')
                ->options(fn (): array => $this->college_class_id
                    ? ExamGroup::where('college_class_id', $this->college_class_id)->where('is_published', true)->pluck('name', 'id')->all()
                    : [])
                ->live()
                ->afterStateUpdated(function (): void {
                    $this->loadGradebook();
                }),
        ];
    }

    public function loadGradebook(): void
    {
        if (! $this->college_class_id || ! $this->exam_group_id) {
            return;
        }

        $this->students = Student::where('college_class_id', $this->college_class_id)->with('user')->get();
        $this->exams = Exam::where('exam_group_id', $this->exam_group_id)->with('subject')->get();

        $this->scores = [];

        $allScores = ExamScore::whereIn('exam_id', $this->exams->pluck('id'))
            ->whereIn('student_id', $this->students->pluck('id'))
            ->get();

        foreach ($allScores as $score) {
            $this->scores[$score->student_id][$score->exam_id] = $score;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn (): StreamedResponse => $this->exportCsv())
                ->disabled(fn (): bool => $this->exams->isEmpty()),
        ];
    }

    public function exportCsv(): StreamedResponse
    {
        $filename = 'gradebook-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            $header = ['Student', 'Roll No'];

            foreach ($this->exams as $exam) {
                $header[] = $exam->subject?->name.' ('.$exam->maximum_marks.')';
                $header[] = 'Grade';
            }

            $header[] = 'Total';
            $header[] = 'Percentage';
            fputcsv($handle, $header);

            foreach ($this->students as $student) {
                $row = [$student->user?->name, $student->roll_number];
                $totalObtained = 0.0;
                $totalMax = 0.0;

                foreach ($this->exams as $exam) {
                    $score = $this->scores[$student->id][$exam->id] ?? null;
                    $marks = $score?->absent ? 'AB' : ($score?->marks_obtained ?? '-');
                    $grade = '-';

                    if ($score && ! $score->absent && $score->marks_obtained !== null) {
                        $percentage = $exam->maximum_marks > 0 ? ((float) $score->marks_obtained / (float) $exam->maximum_marks) * 100 : 0;
                        $gradingLevel = GradingLevel::calculateGrade($percentage, $this->college_class_id);
                        $grade = $gradingLevel?->name ?? '-';
                        $totalObtained += (float) $score->marks_obtained;
                        $totalMax += (float) $exam->maximum_marks;
                    }

                    $row[] = $marks;
                    $row[] = $grade;
                }

                $row[] = $totalObtained.'/'.$totalMax;
                $row[] = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 1).'%' : '-';
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename);
    }
}

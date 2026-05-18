<?php

namespace App\Filament\Faculty\Pages;

use App\Models\CollegeClass;
use App\Models\Exam;
use App\Models\ExamGroup;
use App\Models\ExamScore;
use App\Models\Faculty;
use App\Models\GradingLevel;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TimetableSlot;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MarksEntry extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    protected string $view = 'filament.faculty.pages.marks-entry';

    protected static ?string $navigationLabel = 'Marks Entry';

    protected static ?string $slug = 'marks-entry';

    protected static ?int $navigationSort = 2;

    // --------------------------------------------------------------------------
    // Filter state
    // --------------------------------------------------------------------------

    public ?int $examGroupId = null;

    public ?int $subjectId = null;

    // --------------------------------------------------------------------------
    // Resolved context
    // --------------------------------------------------------------------------

    /** 'fa' | 'sa' | null */
    public ?string $examGroupType = null;

    public ?int $examGroupClassId = null;

    // --------------------------------------------------------------------------
    // Table data
    // --------------------------------------------------------------------------

    /**
     * Ordered exam records for this group+subject.
     * Shape: [['id' => int, 'maximum_marks' => float, 'minimum_marks' => float, 'label' => string], ...]
     *
     * @var array<int, array{id:int, maximum_marks:float, minimum_marks:float, label:string}>
     */
    public array $exams = [];

    /**
     * Students in the exam group's class.
     * Shape: [['id', 'roll_number', 'name'], ...]
     *
     * @var array<int, array{id:int, roll_number:string, name:string}>
     */
    public array $students = [];

    /** @var array<int, array<int, string|null>>  [student_id][exam_id] => raw input string */
    public array $marks = [];

    /** @var array<int, bool>  [student_id => absent] */
    public array $absent = [];

    /** @var array<int, string>  [student_id => writing_language] */
    public array $writingLanguage = [];

    /** @var array<int, string|null>  [student_id => grade name] — updated on recalculate */
    public array $grades = [];

    /** @var array<int, float>  [student_id => total marks] — FA only */
    public array $totals = [];

    // --------------------------------------------------------------------------
    // Grade lookup cache (loaded once per subject selection)
    // --------------------------------------------------------------------------

    /**
     * @var array<int, array{id:int, name:string, min:float, max:float, class_id:int|null}>
     */
    public array $gradingLevelsData = [];

    // --------------------------------------------------------------------------
    // Dropdown options
    // --------------------------------------------------------------------------

    /** @var array<int, string> [id => label] */
    public array $availableExamGroups = [];

    /** @var array<int, string> [id => name] */
    public array $availableSubjects = [];

    /** @var array<int, string> [class_id => name] */
    public array $facultyClasses = [];

    // --------------------------------------------------------------------------
    // Mount
    // --------------------------------------------------------------------------

    public function mount(): void
    {
        $this->loadFacultyClasses();
        $this->loadExamGroups();
    }

    // --------------------------------------------------------------------------
    // Livewire lifecycle hooks
    // --------------------------------------------------------------------------

    public function updatedExamGroupId(): void
    {
        $this->subjectId = null;
        $this->resetTable();
        $this->loadSubjects();
    }

    public function updatedSubjectId(): void
    {
        $this->resetTable();

        if ($this->subjectId) {
            $this->loadMarks();
        }
    }

    /** Called when any marks[studentId][examId] changes */
    public function updatedMarks(mixed $value, string $path): void
    {
        $parts = explode('.', $path);

        if (count($parts) === 2) {
            $this->recalculate((int) $parts[0]);
        }
    }

    /** Called when absent[studentId] is toggled */
    public function updatedAbsent(mixed $value, string $studentId): void
    {
        $this->recalculate((int) $studentId);
    }

    // --------------------------------------------------------------------------
    // Data loaders
    // --------------------------------------------------------------------------

    private function facultyModel(): ?Faculty
    {
        return Faculty::where('user_id', Auth::id())->first();
    }

    private function loadFacultyClasses(): void
    {
        $faculty = $this->facultyModel();

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

    private function loadExamGroups(): void
    {
        $classIds = array_keys($this->facultyClasses);

        if (empty($classIds)) {
            $this->availableExamGroups = [];

            return;
        }

        $this->availableExamGroups = ExamGroup::whereIn('college_class_id', $classIds)
            ->whereNotNull('type')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function (ExamGroup $g): array {
                $className = $this->facultyClasses[$g->college_class_id] ?? '';
                $label = $g->name.' ('.$g->type->shortLabel().')';
                if ($className) {
                    $label .= ' — '.$className;
                }

                return [$g->id => $label];
            })
            ->toArray();
    }

    private function loadSubjects(): void
    {
        $this->availableSubjects = [];

        if (! $this->examGroupId) {
            return;
        }

        $examGroup = ExamGroup::find($this->examGroupId);

        if (! $examGroup) {
            return;
        }

        $this->examGroupClassId = $examGroup->college_class_id;

        // Subjects that actually have Exam records in this group
        $subjectIdsWithExams = Exam::where('exam_group_id', $this->examGroupId)
            ->pluck('subject_id')
            ->unique()
            ->toArray();

        if (empty($subjectIdsWithExams)) {
            return;
        }

        $faculty = $this->facultyModel();

        // Prefer subjects assigned to this faculty
        if ($faculty) {
            $subjects = Subject::whereIn('id', $subjectIdsWithExams)
                ->where('faculty_id', $faculty->id)
                ->pluck('name', 'id')
                ->toArray();

            if (! empty($subjects)) {
                $this->availableSubjects = $subjects;

                return;
            }
        }

        // Fallback: all subjects in the class that have exams in this group
        $this->availableSubjects = Subject::whereIn('id', $subjectIdsWithExams)
            ->where('college_class_id', $examGroup->college_class_id)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function loadMarks(): void
    {
        $this->resetTable();

        if (! $this->examGroupId || ! $this->subjectId) {
            return;
        }

        $examGroup = ExamGroup::find($this->examGroupId);

        if (! $examGroup) {
            return;
        }

        $this->examGroupType = $examGroup->type?->value;
        $this->examGroupClassId = $examGroup->college_class_id;

        // Load exams for this group+subject, ordered by id
        $examModels = Exam::where('exam_group_id', $this->examGroupId)
            ->where('subject_id', $this->subjectId)
            ->orderBy('id')
            ->get();

        if ($examModels->isEmpty()) {
            return;
        }

        // Label exams: FA → Tool 1/2/3/4, SA → Marks
        $this->exams = $examModels->values()->map(function (Exam $e, int $i): array {
            $label = $this->examGroupType === 'fa' ? 'Tool '.($i + 1) : 'Marks';

            return [
                'id' => $e->id,
                'maximum_marks' => (float) $e->maximum_marks,
                'minimum_marks' => (float) $e->minimum_marks,
                'label' => $label,
            ];
        })->toArray();

        // Load students in the class
        $studentModels = Student::where('college_class_id', $this->examGroupClassId)
            ->with('user')
            ->orderBy('roll_number')
            ->get();

        $this->students = $studentModels->map(fn (Student $s): array => [
            'id' => $s->id,
            'roll_number' => $s->roll_number,
            'name' => $s->user?->name ?? '—',
        ])->values()->toArray();

        // Load grading levels for grade auto-lookup
        $this->loadGradingLevels($this->examGroupClassId);

        // Initialize marks/absent/writingLanguage arrays
        $studentIds = $studentModels->pluck('id')->toArray();
        $examIds = $examModels->pluck('id')->toArray();

        foreach ($studentIds as $sid) {
            $this->absent[$sid] = false;
            $this->writingLanguage[$sid] = '';
            $this->totals[$sid] = 0.0;
            $this->grades[$sid] = '—';
            foreach ($examIds as $eid) {
                $this->marks[$sid][$eid] = '';
            }
        }

        // Load existing scores
        $existingScores = ExamScore::whereIn('exam_id', $examIds)
            ->whereIn('student_id', $studentIds)
            ->get();

        foreach ($existingScores as $score) {
            $sid = $score->student_id;
            $eid = $score->exam_id;

            if ($score->absent) {
                $this->absent[$sid] = true;
            } elseif ($score->marks_obtained !== null) {
                $this->marks[$sid][$eid] = rtrim(rtrim((string) $score->marks_obtained, '0'), '.');
            }

            // Writing language — stored per score; use whichever has a value
            if ($score->writing_language) {
                $this->writingLanguage[$sid] = $score->writing_language;
            }
        }

        // Compute initial grades/totals
        foreach ($studentIds as $sid) {
            $this->recalculate($sid);
        }
    }

    // --------------------------------------------------------------------------
    // Grade lookup (PHP-side, no extra DB queries per keystroke)
    // --------------------------------------------------------------------------

    private function loadGradingLevels(int $classId): void
    {
        $this->gradingLevelsData = GradingLevel::where(function ($q) use ($classId): void {
            $q->where('college_class_id', $classId)
                ->orWhereNull('college_class_id');
        })
            ->orderByRaw('college_class_id IS NULL ASC') // class-specific first
            ->orderByDesc('max_score')
            ->get()
            ->map(fn (GradingLevel $gl): array => [
                'id' => $gl->id,
                'name' => $gl->name,
                'min' => (float) $gl->min_score,
                'max' => (float) $gl->max_score,
                'class_id' => $gl->college_class_id,
            ])
            ->toArray();
    }

    private function lookupGrade(float $percentage): ?string
    {
        $pct = max(0.0, min(100.0, $percentage));

        // Class-specific first
        foreach ($this->gradingLevelsData as $gl) {
            if ($gl['class_id'] !== null && $pct >= $gl['min'] && $pct <= $gl['max']) {
                return $gl['name'];
            }
        }
        // Global fallback
        foreach ($this->gradingLevelsData as $gl) {
            if ($gl['class_id'] === null && $pct >= $gl['min'] && $pct <= $gl['max']) {
                return $gl['name'];
            }
        }

        return null;
    }

    private function lookupGradeLevelId(float $percentage): ?int
    {
        $pct = max(0.0, min(100.0, $percentage));

        foreach ($this->gradingLevelsData as $gl) {
            if ($gl['class_id'] !== null && $pct >= $gl['min'] && $pct <= $gl['max']) {
                return $gl['id'];
            }
        }
        foreach ($this->gradingLevelsData as $gl) {
            if ($gl['class_id'] === null && $pct >= $gl['min'] && $pct <= $gl['max']) {
                return $gl['id'];
            }
        }

        return null;
    }

    // --------------------------------------------------------------------------
    // Recalculate total + grade for one student
    // --------------------------------------------------------------------------

    public function recalculate(int $studentId): void
    {
        if ($this->absent[$studentId] ?? false) {
            $this->totals[$studentId] = 0.0;
            $this->grades[$studentId] = 'AB';

            return;
        }

        $total = 0.0;
        $maxTotal = 0.0;
        $allFilled = true;

        foreach ($this->exams as $exam) {
            $raw = $this->marks[$studentId][$exam['id']] ?? '';
            $maxTotal += $exam['maximum_marks'];

            if ($raw !== '' && $raw !== null) {
                $val = (float) $raw;
                // Clamp to max
                $val = min($val, $exam['maximum_marks']);
                $total += $val;
            } else {
                $allFilled = false;
            }
        }

        $this->totals[$studentId] = $total;

        if ($maxTotal > 0 && ($allFilled || $total > 0)) {
            $pct = ($total / $maxTotal) * 100;
            $this->grades[$studentId] = $this->lookupGrade($pct) ?? '—';
        } else {
            $this->grades[$studentId] = '—';
        }
    }

    // --------------------------------------------------------------------------
    // Save
    // --------------------------------------------------------------------------

    public function save(): void
    {
        if (empty($this->students) || empty($this->exams)) {
            Notification::make()->title('No data to save')->warning()->send();

            return;
        }

        $userId = Auth::id();
        $saved = 0;

        foreach ($this->students as $student) {
            $sid = $student['id'];
            $isAbsent = (bool) ($this->absent[$sid] ?? false);
            $writingLang = trim($this->writingLanguage[$sid] ?? '') ?: null;

            // Calculate total and grade for this student at save time
            $total = 0.0;
            $maxTotal = 0.0;

            foreach ($this->exams as $exam) {
                $raw = $this->marks[$sid][$exam['id']] ?? '';
                $maxTotal += $exam['maximum_marks'];

                if (! $isAbsent && $raw !== '' && $raw !== null) {
                    $total += min((float) $raw, $exam['maximum_marks']);
                }
            }

            $gradeLevelId = null;

            if (! $isAbsent && $maxTotal > 0) {
                $pct = ($total / $maxTotal) * 100;
                $gradeLevelId = $this->lookupGradeLevelId($pct);
            }

            foreach ($this->exams as $exam) {
                $eid = $exam['id'];
                $raw = $this->marks[$sid][$eid] ?? '';

                $marksObtained = null;

                if (! $isAbsent && $raw !== '' && $raw !== null) {
                    $marksObtained = min((float) $raw, $exam['maximum_marks']);
                }

                ExamScore::updateOrCreate(
                    [
                        'exam_id' => $eid,
                        'student_id' => $sid,
                    ],
                    [
                        'marks_obtained' => $marksObtained,
                        'absent' => $isAbsent,
                        'grading_level_id' => $isAbsent ? null : $gradeLevelId,
                        'writing_language' => $writingLang,
                        'entered_by' => $userId,
                    ]
                );

                $saved++;
            }
        }

        $className = $this->facultyClasses[$this->examGroupClassId ?? 0] ?? '';

        Notification::make()
            ->title('Marks saved successfully')
            ->body("{$saved} score records saved for {$className}.")
            ->success()
            ->send();
    }

    // --------------------------------------------------------------------------
    // Helpers
    // --------------------------------------------------------------------------

    private function resetTable(): void
    {
        $this->exams = [];
        $this->students = [];
        $this->marks = [];
        $this->absent = [];
        $this->writingLanguage = [];
        $this->grades = [];
        $this->totals = [];
        $this->gradingLevelsData = [];
        $this->examGroupType = null;
        $this->examGroupClassId = null;
    }

    public function getMaxTotalProperty(): float
    {
        return (float) array_sum(array_column($this->exams, 'maximum_marks'));
    }
}

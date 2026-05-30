<?php

namespace App\Filament\Student\Pages;

use App\Models\ExamGroup;
use App\Models\GradingLevel;
use App\Models\Student;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MyResults extends Page
{
    protected string $view = 'filament.student.pages.my-results';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'My Results';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'My Results';

    public bool $hasProfile = false;

    public bool $hasResults = false;

    public array $examGroups = [];

    public array $openGroups = [];

    public array $annualResult = [];

    public array $profile = [];

    // --------------------------------------------------------------------------
    // Lifecycle
    // --------------------------------------------------------------------------

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $student = $user?->studentProfile;

        if (! $student) {
            return;
        }

        $this->hasProfile = true;
        $student->loadMissing(['collegeClass', 'academicYear']);

        $this->profile = [
            'name' => $user->name,
            'class' => $student->collegeClass?->name ?? '—',
        ];

        $this->loadResults($student);
    }

    // --------------------------------------------------------------------------
    // Actions
    // --------------------------------------------------------------------------

    public function toggleGroup(int $groupId): void
    {
        if (in_array($groupId, $this->openGroups, true)) {
            $this->openGroups = array_values(
                array_filter($this->openGroups, fn ($id) => $id !== $groupId)
            );
        } else {
            $this->openGroups[] = $groupId;
        }
    }

    // --------------------------------------------------------------------------
    // Data loaders
    // --------------------------------------------------------------------------

    private function loadResults(Student $student): void
    {
        $query = ExamGroup::where('college_class_id', $student->college_class_id)
            ->where('is_published', true);

        // Scope to student's academic year when set, but also include groups with no year set
        if ($student->academic_year_id) {
            $query->where(function ($q) use ($student): void {
                $q->where('academic_year_id', $student->academic_year_id)
                    ->orWhereNull('academic_year_id');
            });
        }

        $groups = $query
            ->with([
                'exams' => fn ($q) => $q->orderBy('id')->with('subject'),
                'exams.examScores' => fn ($q) => $q->where('student_id', $student->id),
            ])
            // FA first, then SA, then ungrouped — CASE WHEN is standard SQL (SQLite ✓)
            ->orderByRaw("CASE WHEN type = 'fa' THEN 0 WHEN type = 'sa' THEN 1 ELSE 2 END")
            ->orderBy('conducted_date')
            ->orderBy('id')
            ->get();

        if ($groups->isEmpty()) {
            return;
        }

        $this->hasResults = true;
        $classId = $student->college_class_id;

        $cards = [];
        $faObtained = 0.0;
        $faMax = 0.0;
        $saObtained = 0.0;
        $saMax = 0.0;
        $hasSa = false;

        foreach ($groups as $group) {
            $card = $this->buildGroupCard($group, $classId);
            $cards[] = $card;

            $type = $group->type?->value;

            if ($type === 'fa') {
                $faObtained += $card['obtained'];
                $faMax += $card['maximum'];
            } elseif ($type === 'sa') {
                $saObtained += $card['obtained'];
                $saMax += $card['maximum'];
                $hasSa = true;
            }
        }

        $this->examGroups = $cards;

        if ($hasSa) {
            $grandObtained = $faObtained + $saObtained;
            $grandMax = $faMax + $saMax;
            $grandPct = $grandMax > 0 ? round(($grandObtained / $grandMax) * 100, 1) : 0.0;
            $annualGrade = GradingLevel::calculateGrade($grandPct, $classId);

            $this->annualResult = [
                'show' => true,
                'fa_obtained' => $faObtained,
                'fa_max' => $faMax,
                'sa_obtained' => $saObtained,
                'sa_max' => $saMax,
                'grand_obtained' => $grandObtained,
                'grand_max' => $grandMax,
                'percentage' => $grandPct,
                'grade' => $annualGrade?->name ?? '—',
                'grade_class' => $this->gradeClass($annualGrade?->name),
            ];
        }
    }

    // --------------------------------------------------------------------------
    // Card builder
    // --------------------------------------------------------------------------

    private function buildGroupCard(ExamGroup $group, int $classId): array
    {
        // Exams already eager-loaded, sorted by id
        $exams = $group->exams;

        // Group by subject so FA "tools" (multiple exams per subject) are combined
        $bySubject = $exams->groupBy('subject_id');
        $maxTools = max(1, (int) ($bySubject->max(fn ($e) => $e->count()) ?? 1));

        $subjectRows = [];
        $groupObtained = 0.0;
        $groupMaximum = 0.0;

        foreach ($bySubject as $subjectExams) {
            $subjectName = $subjectExams->first()->subject?->name ?? '—';
            $tools = [];
            $subObtained = 0.0;
            $subMax = 0.0;

            foreach ($subjectExams as $exam) {
                $score = $exam->examScores->first();
                $absent = (bool) ($score?->absent ?? false);
                $marks = ($score && ! $absent && $score->marks_obtained !== null)
                    ? (float) $score->marks_obtained
                    : null;
                $max = (float) $exam->maximum_marks;

                $tools[] = [
                    'marks' => $marks,
                    'max' => $max,
                    'absent' => $absent,
                ];

                if ($marks !== null) {
                    $subObtained += $marks;
                }
                $subMax += $max;
            }

            // Pad all rows to the same column count so the table aligns
            while (count($tools) < $maxTools) {
                $tools[] = ['marks' => null, 'max' => 0.0, 'absent' => false];
            }

            $subPct = $subMax > 0 ? round(($subObtained / $subMax) * 100, 1) : 0.0;
            $subGrade = GradingLevel::calculateGrade($subPct, $classId);

            $subjectRows[] = [
                'subject' => $subjectName,
                'tools' => $tools,
                'obtained' => $subObtained,
                'maximum' => $subMax,
                'grade' => $subGrade?->name ?? '—',
                'grade_class' => $this->gradeClass($subGrade?->name),
            ];

            $groupObtained += $subObtained;
            $groupMaximum += $subMax;
        }

        $groupPct = $groupMaximum > 0 ? round(($groupObtained / $groupMaximum) * 100, 1) : 0.0;
        $groupGrade = GradingLevel::calculateGrade($groupPct, $classId);
        $type = $group->type?->value; // 'fa', 'sa', or null

        return [
            'id' => $group->id,
            'name' => $group->name,
            'type' => $type,
            'type_label' => $group->type?->shortLabel(),        // 'FA', 'SA', or null
            'conducted_date' => $group->conducted_date?->format('d M Y') ?? '—',
            'obtained' => $groupObtained,
            'maximum' => $groupMaximum,
            'percentage' => $groupPct,
            'grade' => $groupGrade?->name ?? '—',
            'grade_class' => $this->gradeClass($groupGrade?->name),
            'max_tools' => $maxTools,
            'subjects' => $subjectRows,
        ];
    }

    // --------------------------------------------------------------------------
    // Grade colour helper
    // --------------------------------------------------------------------------

    private function gradeClass(?string $grade): string
    {
        if (! $grade || $grade === '—') {
            return 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400';
        }

        return match (true) {
            in_array($grade, ['A+', 'A1', 'A2', 'O', 'S', 'Ex']) => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            in_array($grade, ['A', 'B+', 'B1', 'B2']) => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            in_array($grade, ['B', 'C+', 'C', 'C1', 'C2']) => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
            in_array($grade, ['D+', 'D', 'D1', 'D2']) => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
            in_array($grade, ['E', 'F', 'U']) => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            default => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300',
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

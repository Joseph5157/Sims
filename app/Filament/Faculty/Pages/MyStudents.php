<?php

namespace App\Filament\Faculty\Pages;

use App\Models\Attendance;
use App\Models\CollegeClass;
use App\Models\Exam;
use App\Models\ExamScore;
use App\Models\Faculty;
use App\Models\GradingLevel;
use App\Models\Student;
use App\Models\TimetableSlot;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MyStudents extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected string $view = 'filament.faculty.pages.my-students';

    protected static ?string $navigationLabel = 'My Students';

    protected static ?string $slug = 'students';

    protected static ?int $navigationSort = 4;

    // --------------------------------------------------------------------------
    // List state
    // --------------------------------------------------------------------------

    /** @var array<int, array{...}> flat list of all students across faculty classes */
    public array $students = [];

    /** @var array<int, string> [id => name] */
    public array $facultyClasses = [];

    /** Filter: selected class */
    public ?int $filterClassId = null;

    /** Filter: search term */
    public string $search = '';

    // --------------------------------------------------------------------------
    // Modal state
    // --------------------------------------------------------------------------

    public ?int $selectedStudentId = null;

    /** Full student profile data for the modal */
    public array $modalStudent = [];

    /** Attendance summary [total, present, absent, late, excused, percentage] */
    public array $modalAttendance = [];

    /** Recent exam scores [ [exam, subject, marks, max, grade, pct], ... ] */
    public array $modalScores = [];

    // --------------------------------------------------------------------------
    // Mount
    // --------------------------------------------------------------------------

    public function mount(): void
    {
        $this->loadFacultyClasses();
        $this->loadStudents();
    }

    // --------------------------------------------------------------------------
    // Lifecycle hooks
    // --------------------------------------------------------------------------

    public function updatedFilterClassId(): void
    {
        $this->loadStudents();
    }

    public function updatedSearch(): void
    {
        $this->loadStudents();
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

    public function loadStudents(): void
    {
        $classIds = $this->filterClassId
            ? [$this->filterClassId]
            : array_keys($this->facultyClasses);

        if (empty($classIds)) {
            $this->students = [];

            return;
        }

        $query = Student::whereIn('college_class_id', $classIds)
            ->with(['user', 'collegeClass']);

        if (! empty(trim($this->search))) {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term): void {
                $q->where('roll_number', 'like', $term)
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $term));
            });
        }

        $students = $query->orderBy('college_class_id')->orderBy('roll_number')->get();

        // Batch-load attendance percentages & latest grade per student
        $studentIds = $students->pluck('id')->all();

        // Attendance counts in one query
        $attendanceCounts = Attendance::selectRaw(
            "student_id,
             COUNT(*) as total,
             SUM(CASE WHEN status IN ('present', 'late', 'excused') THEN 1 ELSE 0 END) as attended"
        )
            ->whereIn('student_id', $studentIds)
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');

        // Latest exam score per student (most recent exam date)
        $latestScores = ExamScore::whereIn('student_id', $studentIds)
            ->with(['exam.subject', 'exam.examGroup', 'gradingLevel'])
            ->join('exams', 'exam_scores.exam_id', '=', 'exams.id')
            ->join('exam_groups', 'exams.exam_group_id', '=', 'exam_groups.id')
            ->where('exam_groups.is_published', true)
            ->orderByDesc('exams.date')
            ->get(['exam_scores.*'])
            ->unique('student_id')
            ->keyBy('student_id');

        $this->students = $students->map(function (Student $s) use ($attendanceCounts, $latestScores): array {
            $att = $attendanceCounts[$s->id] ?? null;
            $total = $att ? (int) $att['total'] : 0;
            $pct = ($total > 0 && $att) ? round(($att['attended'] / $total) * 100, 1) : null;

            $score = $latestScores[$s->id] ?? null;
            $gradeName = $score?->gradingLevel?->name ?? ($score ? '—' : null);

            return [
                'id' => $s->id,
                'name' => $s->user?->name ?? '—',
                'roll_number' => $s->roll_number,
                'admission_number' => $s->admission_number,
                'class_name' => $s->collegeClass?->name ?? '—',
                'class_id' => $s->college_class_id,
                'attendance_pct' => $pct,
                'attendance_total' => $total,
                'last_grade' => $gradeName,
                'last_exam_name' => $score?->exam?->subject?->name,
                'status' => $s->status?->value ?? 'active',
                'status_label' => $s->status?->label() ?? 'Active',
                'status_color' => $s->status?->color() ?? 'success',
            ];
        })->values()->toArray();
    }

    // --------------------------------------------------------------------------
    // Modal
    // --------------------------------------------------------------------------

    public function openModal(int $studentId): void
    {
        $student = Student::with(['user', 'collegeClass', 'department'])->find($studentId);

        if (! $student) {
            return;
        }

        $this->selectedStudentId = $studentId;

        // Full profile
        $this->modalStudent = [
            'name' => $student->user?->name ?? '—',
            'email' => $student->user?->email ?? '—',
            'roll_number' => $student->roll_number ?? '—',
            'admission_number' => $student->admission_number ?? '—',
            'class_name' => $student->collegeClass?->name ?? '—',
            'department' => $student->department?->name ?? '—',
            'gender' => $student->gender?->label() ?? '—',
            'blood_group' => $student->blood_group ?? '—',
            'phone' => $student->phone ?? '—',
            'dob' => $student->date_of_birth?->format('d M Y') ?? '—',
            'admission_year' => $student->admission_year ?? '—',
            'status' => $student->status?->label() ?? 'Active',
            'status_color' => $student->status?->color() ?? 'success',
        ];

        // Attendance summary
        $attRecords = Attendance::where('student_id', $studentId)
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $present = (int) ($attRecords['present'] ?? 0);
        $absent = (int) ($attRecords['absent'] ?? 0);
        $late = (int) ($attRecords['late'] ?? 0);
        $excused = (int) ($attRecords['excused'] ?? 0);
        $total = $present + $absent + $late + $excused;
        $attended = $present + $late + $excused;

        $this->modalAttendance = [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'percentage' => $total > 0 ? round(($attended / $total) * 100, 1) : 0,
        ];

        // Recent exam scores (last 8, published only)
        $scores = ExamScore::where('student_id', $studentId)
            ->with(['exam.subject', 'exam.examGroup', 'gradingLevel'])
            ->join('exams', 'exam_scores.exam_id', '=', 'exams.id')
            ->join('exam_groups', 'exams.exam_group_id', '=', 'exam_groups.id')
            ->where('exam_groups.is_published', true)
            ->orderByDesc('exams.date')
            ->limit(8)
            ->get(['exam_scores.*']);

        $this->modalScores = $scores->map(function (ExamScore $sc) use ($student): array {
            $maxMarks = (float) ($sc->exam?->maximum_marks ?? 0);
            $obtained = (float) ($sc->marks_obtained ?? 0);
            $pct = ($maxMarks > 0 && ! $sc->absent) ? round(($obtained / $maxMarks) * 100, 1) : null;
            $grade = $sc->gradingLevel?->name;

            if (! $grade && $pct !== null) {
                $gl = GradingLevel::calculateGrade($pct, $student->college_class_id);
                $grade = $gl?->name;
            }

            return [
                'exam_group' => $sc->exam?->examGroup?->name ?? '—',
                'subject' => $sc->exam?->subject?->name ?? '—',
                'marks' => $sc->absent ? 'AB' : ($sc->marks_obtained ?? '—'),
                'max_marks' => $sc->exam?->maximum_marks ?? '—',
                'percentage' => $pct,
                'grade' => $grade ?? '—',
                'absent' => (bool) $sc->absent,
            ];
        })->toArray();
    }

    public function closeModal(): void
    {
        $this->selectedStudentId = null;
        $this->modalStudent = [];
        $this->modalAttendance = [];
        $this->modalScores = [];
    }
}

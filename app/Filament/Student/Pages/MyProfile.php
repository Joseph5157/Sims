<?php

namespace App\Filament\Student\Pages;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ExamGroup;
use App\Models\ExamScore;
use App\Models\GradingLevel;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TimetableSlot;
use App\Models\User;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MyProfile extends Page
{
    protected string $view = 'filament.student.pages.my-profile';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'My Profile';

    protected static ?int $navigationSort = 7;

    protected static ?string $title = 'My Profile';

    public bool $hasProfile = false;

    public array $header = [];   // avatar + name + badges

    public array $personal = [];   // DOB, gender, phone…

    public array $academic = [];   // class, dept, roll no…

    public array $subjects = [];   // subject chips

    public array $teachers = [];   // faculty rows

    public array $quickStats = [];   // attendance, grade, fees

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

        $student->loadMissing(['user', 'collegeClass.department', 'department', 'academicYear']);

        $this->buildHeader($student, $user);
        $this->buildPersonal($student, $user);
        $this->buildAcademic($student);
        $this->buildSubjectsAndTeachers($student);
        $this->buildQuickStats($student);
    }

    // --------------------------------------------------------------------------
    // Header: avatar + name + roll + class + status
    // --------------------------------------------------------------------------

    private function buildHeader(Student $student, User $user): void
    {
        // Avatar: Spatie MediaLibrary photo if uploaded, else initials
        $photoUrl = $student->getFirstMediaUrl('profile-photo');
        $initials = strtoupper(substr($user->name, 0, 1));

        $statusValue = $student->status?->value ?? 'active';
        $statusLabel = $student->status?->label() ?? 'Active';
        $statusClass = match ($statusValue) {
            'active' => 'bg-green-400/20 text-green-200',
            'alumni' => 'bg-blue-400/20 text-blue-200',
            'transferred' => 'bg-yellow-400/20 text-yellow-200',
            'dropped' => 'bg-red-400/20 text-red-200',
            default => 'bg-white/20 text-white',
        };

        $this->header = [
            'photo_url' => $photoUrl ?: null,
            'initials' => $initials,
            'name' => $user->name,
            'roll_number' => $student->roll_number ?? '—',
            'admission_no' => $student->admission_number ?? '—',
            'class' => $student->collegeClass?->name ?? '—',
            'department' => $student->department?->name
                ?? $student->collegeClass?->department?->name
                ?? '—',
            'academic_year' => $student->academicYear?->name
                ?? AcademicYear::where('is_current', true)->value('name')
                ?? '—',
            'status_label' => $statusLabel,
            'status_class' => $statusClass,
        ];
    }

    // --------------------------------------------------------------------------
    // Personal information
    // --------------------------------------------------------------------------

    private function buildPersonal(Student $student, User $user): void
    {
        $this->personal = [
            'date_of_birth' => $student->date_of_birth
                ? Carbon::parse($student->date_of_birth)->format('d M Y')
                : '—',
            'gender' => $student->gender?->label() ?? '—',
            'blood_group' => $student->blood_group ?? '—',
            'phone' => $student->phone ?? '—',
            'email' => $user->email ?? '—',
            'address' => $student->address ?? '—',
            'admission_year' => $student->admission_year ?? '—',
        ];
    }

    // --------------------------------------------------------------------------
    // Academic information
    // --------------------------------------------------------------------------

    private function buildAcademic(Student $student): void
    {
        $this->academic = [
            'class' => $student->collegeClass?->name ?? '—',
            'section' => $student->collegeClass?->section ?? '—',
            'semester' => $student->collegeClass?->semester ?? '—',
            'department' => $student->department?->name
                ?? $student->collegeClass?->department?->name
                ?? '—',
            'academic_year' => $student->academicYear?->name
                ?? AcademicYear::where('is_current', true)->value('name')
                ?? '—',
            'roll_number' => $student->roll_number ?? '—',
            'admission_no' => $student->admission_number ?? '—',
            'admission_year' => $student->admission_year ?? '—',
        ];
    }

    // --------------------------------------------------------------------------
    // Subjects + Teachers
    // --------------------------------------------------------------------------

    private function buildSubjectsAndTeachers(Student $student): void
    {
        // Load subjects for student's class with their faculty
        $subjects = Subject::where('college_class_id', $student->college_class_id)
            ->with('faculty.user')
            ->orderBy('name')
            ->get();

        // Subject chips
        $this->subjects = $subjects->map(fn (Subject $s): array => [
            'name' => $s->name,
            'code' => $s->code,
            'type' => $s->subject_type?->value,
            'badge_class' => $this->subjectBadgeClass($s->subject_type?->value),
        ])->toArray();

        // Teachers: subjects with faculty directly assigned
        $teacherRows = [];
        $seenFaculty = [];

        foreach ($subjects as $subject) {
            if ($subject->faculty_id && $subject->faculty?->user) {
                $key = $subject->faculty_id.'-'.$subject->id;
                if (! isset($seenFaculty[$key])) {
                    $seenFaculty[$key] = true;
                    $teacherRows[] = [
                        'faculty_name' => $subject->faculty->user->name,
                        'subject' => $subject->name,
                        'subject_type' => $subject->subject_type?->value,
                        'badge_class' => $this->subjectBadgeClass($subject->subject_type?->value),
                        'type_label' => $subject->subject_type?->label() ?? '',
                    ];
                }
            }
        }

        // Also pull from timetable slots if subjects have no direct assignment
        if (empty($teacherRows)) {
            $slots = TimetableSlot::where('college_class_id', $student->college_class_id)
                ->whereNotNull('faculty_id')
                ->with(['subject', 'faculty.user'])
                ->get();

            foreach ($slots as $slot) {
                if (! $slot->faculty?->user) {
                    continue;
                }
                $key = ($slot->faculty_id ?? 0).'-'.($slot->subject_id ?? 0);
                if (! isset($seenFaculty[$key])) {
                    $seenFaculty[$key] = true;
                    $teacherRows[] = [
                        'faculty_name' => $slot->faculty->user->name,
                        'subject' => $slot->subject?->name ?? '—',
                        'subject_type' => $slot->subject?->subject_type?->value,
                        'badge_class' => $this->subjectBadgeClass($slot->subject?->subject_type?->value),
                        'type_label' => $slot->subject?->subject_type?->label() ?? '',
                    ];
                }
            }
        }

        $this->teachers = $teacherRows;
    }

    // --------------------------------------------------------------------------
    // Quick stats
    // --------------------------------------------------------------------------

    private function buildQuickStats(Student $student): void
    {
        // Attendance percentage
        $counts = Attendance::where('student_id', $student->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(status = 'present') as present,
                SUM(status = 'late')    as late,
                SUM(status = 'excused') as excused
            ")
            ->first();

        $total = (int) ($counts->total ?? 0);
        $attended = (int) ($counts->present ?? 0)
                  + (int) ($counts->late ?? 0)
                  + (int) ($counts->excused ?? 0);
        $attPct = $total > 0 ? round(($attended / $total) * 100, 1) : 0.0;
        $attColor = $attPct >= 75 ? 'text-green-600 dark:text-green-400'
                  : ($attPct >= 60 ? 'text-yellow-600 dark:text-yellow-400'
                                   : 'text-red-600 dark:text-red-400');

        // Latest published exam grade
        $latestGroup = ExamGroup::where('college_class_id', $student->college_class_id)
            ->where('is_published', true)
            ->orderBy('conducted_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $latestGrade = '—';
        $gradeClass = 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400';

        if ($latestGroup) {
            $examIds = $latestGroup->exams()->pluck('id');
            $scores = ExamScore::whereIn('exam_id', $examIds)
                ->where('student_id', $student->id)
                ->where('absent', false)
                ->whereNotNull('marks_obtained')
                ->with('exam')
                ->get();

            if ($scores->isNotEmpty()) {
                $obtained = $scores->sum(fn ($s) => (float) $s->marks_obtained);
                $maximum = $scores->sum(fn ($s) => (float) ($s->exam?->maximum_marks ?? 0));
                $pct = $maximum > 0 ? round(($obtained / $maximum) * 100, 1) : 0.0;
                $gradeObj = GradingLevel::calculateGrade($pct, $student->college_class_id);
                $latestGrade = $gradeObj?->name ?? '—';
                $gradeClass = $this->gradeClass($latestGrade);
            }
        }

        // Fee balance
        $outstanding = $student->getOutstandingAmount();
        $feeLabel = $outstanding <= 0 ? 'Fully Paid' : '₹'.number_format($outstanding, 2).' Due';
        $feeColor = $outstanding <= 0
            ? 'text-green-600 dark:text-green-400'
            : 'text-red-600 dark:text-red-400';

        $this->quickStats = [
            'attendance_pct' => $attPct,
            'attendance_label' => $total > 0 ? $attPct.'%' : '—',
            'attendance_color' => $attColor,
            'latest_grade' => $latestGrade,
            'grade_class' => $gradeClass,
            'latest_exam' => $latestGroup?->name ?? '—',
            'fee_label' => $feeLabel,
            'fee_color' => $feeColor,
        ];
    }

    // --------------------------------------------------------------------------
    // Colour helpers
    // --------------------------------------------------------------------------

    private function subjectBadgeClass(?string $type): string
    {
        return match ($type) {
            'theory' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'practical' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
            'elective' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
            'project' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
            default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
        };
    }

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

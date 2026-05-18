<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Admission;
use App\Models\CollegeClass;
use App\Models\Department;
use App\Models\Enquiry;
use App\Models\Event;
use App\Models\Exam;
use App\Models\ExamGroup;
use App\Models\ExamScore;
use App\Models\Faculty;
use App\Models\FeeCategory;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\GradingLevel;
use App\Models\Guardian;
use App\Models\LessonPlan;
use App\Models\Notice;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TimetableSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TenantDatabaseSeeder extends Seeder
{
    // --------------------------------------------------------------------------
    // Entry point
    // --------------------------------------------------------------------------

    public function run(): void
    {
        // Skip tenants whose database hasn't been fully migrated yet
        if (! Schema::hasTable('school_settings') || ! Schema::hasTable('academic_years')) {
            $this->command->warn('  ⚠  Skipping – required tables not found (run tenants:migrate first).');

            return;
        }

        $this->command->info('🌱 Starting comprehensive SIMS seed...');

        // ── 0. Wipe all application tables (safe re-run) ───────────────────
        $this->wipe();

        // ── 1. Shield permissions + roles ──────────────────────────────────
        $this->command->info('  → Generating Shield permissions…');
        try {
            Artisan::call('shield:generate', [
                '--all' => true,
                '--panel' => 'admin',
                '--option' => 'permissions',
            ]);
        } catch (\Exception $e) {
            $this->command->warn('     Shield skipped: '.$e->getMessage());
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin',   'guard_name' => 'web']);
        $facultyRole = Role::firstOrCreate(['name' => 'faculty', 'guard_name' => 'web']);
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $parentRole = Role::firstOrCreate(['name' => 'parent',  'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // ── 2. School settings ──────────────────────────────────────────────
        $this->command->info('  → School settings…');
        SchoolSetting::create([
            'school_name' => "St. Mary's High School",
            'school_address' => '123 Education Lane, Hyderabad, Telangana 500001',
            'school_phone' => '+91 40 2345 6789',
            'school_email' => 'info@stmarys.edu.in',
            'principal_name' => 'Dr. Sarah Johnson',
            'school_motto' => 'Excellence in Education',
            'affiliation_number' => 'AP123456',
            'established_year' => '1995',
            'report_card_color' => '#7c3aed',
            'report_card_footer_text' => "St. Mary's High School · Excellence in Education · Hyderabad",
        ]);

        // ── 3. Academic year ────────────────────────────────────────────────
        $this->command->info('  → Academic year…');
        $ay = AcademicYear::create([
            'name' => '2025-26',
            'start_year' => 2025,
            'end_year' => 2026,
            'is_current' => true,
        ]);

        // ── 4. Departments ──────────────────────────────────────────────────
        $this->command->info('  → Departments…');
        $csDept = Department::create(['name' => 'Computer Science', 'code' => 'CS',   'description' => 'Computer Science and Engineering']);
        $mathDept = Department::create(['name' => 'Mathematics',      'code' => 'MATH', 'description' => 'Mathematics and Statistics']);
        $sciDept = Department::create(['name' => 'Science',          'code' => 'SCI',  'description' => 'Natural Sciences']);

        // ── 5. College classes ──────────────────────────────────────────────
        $this->command->info('  → College classes…');
        $csY1 = $this->makeClass('CS Year 1', $csDept, 1, $ay);
        $csY2 = $this->makeClass('CS Year 2', $csDept, 3, $ay);
        $mathY1 = $this->makeClass('Math Year 1', $mathDept, 1, $ay);
        $mathY2 = $this->makeClass('Math Year 2', $mathDept, 3, $ay);
        $sciY1 = $this->makeClass('Science Year 1', $sciDept, 1, $ay);
        $sciY2 = $this->makeClass('Science Year 2', $sciDept, 3, $ay);

        // ── 6. Admin user ───────────────────────────────────────────────────
        $this->command->info('  → Users…');
        $admin = $this->makeUser('Dr. Sarah Johnson', 'admin@sims.test', $adminRole);

        // ── 7. Faculty users + records ──────────────────────────────────────
        $facDefs = [
            ['Prof. Arun Sharma',    'faculty@sims.test',  'FAC001', $csDept,   'M.Tech Computer Science',  'Programming Languages',       'Associate Professor', '2020-08-01'],
            ['Dr. Meena Krishnan',   'faculty2@sims.test', 'FAC002', $csDept,   'PhD Data Structures',      'Algorithms & Data Structures', 'Assistant Professor', '2019-06-15'],
            ['Prof. Rajesh Verma',   'faculty3@sims.test', 'FAC003', $mathDept, 'M.Sc Mathematics',         'Applied Mathematics',          'Professor',           '2018-07-01'],
            ['Dr. Sunita Reddy',     'faculty4@sims.test', 'FAC004', $sciDept,  'PhD Physics',              'Quantum Mechanics',            'Senior Professor',    '2017-06-10'],
            ['Prof. Vikram Nair',    'faculty5@sims.test', 'FAC005', $sciDept,  'M.Sc Chemistry',           'Organic Chemistry',            'Assistant Professor', '2021-08-20'],
        ];

        $facRecs = [];
        foreach ($facDefs as $i => $fd) {
            $u = $this->makeUser($fd[0], $fd[1], $facultyRole);
            $facRecs[] = Faculty::create([
                'user_id' => $u->id,
                'department_id' => $fd[3]->id,
                'employee_id' => $fd[2],
                'qualification' => $fd[4],
                'joining_date' => $fd[7],
                'phone' => '+91 9876'.str_pad((string) (543210 + $i), 6, '0', STR_PAD_LEFT),
                'specialization' => $fd[5],
                'designation' => $fd[6],
            ]);
        }
        [$fac1, $fac2, $fac3, $fac4, $fac5] = $facRecs;

        // ── 8. Students ─────────────────────────────────────────────────────
        $stuDefs = [
            // CS Year 1
            ['Arjun Patel',         'student@sims.test',   'STU001', 'ADM2025001', $csY1,   $csDept,   '2005-03-15', 'male',   'O+',  'Flat 201, Green Valley, Hyderabad'],
            ['Priya Nambiar',       'student2@sims.test',  'STU002', 'ADM2025002', $csY1,   $csDept,   '2005-07-22', 'female', 'A+',  'H.No 45, Banjara Hills, Hyderabad'],
            ['Rohit Mehta',         'student3@sims.test',  'STU003', 'ADM2025003', $csY1,   $csDept,   '2005-01-10', 'male',   'B+',  '12, Jubilee Hills, Hyderabad'],
            ['Divya Krishnamurthy', 'student4@sims.test',  'STU004', 'ADM2025004', $csY1,   $csDept,   '2005-11-08', 'female', 'AB+', '78, Madhapur, Hyderabad'],
            ['Karthik Subramaniam', 'student5@sims.test',  'STU005', 'ADM2025005', $csY1,   $csDept,   '2005-05-25', 'male',   'O-',  '34, Gachibowli, Hyderabad'],
            // Math Year 1
            ['Sneha Iyer',          'student6@sims.test',  'STU006', 'ADM2025006', $mathY1, $mathDept, '2005-09-12', 'female', 'A-',  '56, Indiranagar, Bangalore'],
            ['Aditya Bose',         'student7@sims.test',  'STU007', 'ADM2025007', $mathY1, $mathDept, '2005-02-28', 'male',   'B-',  '89, Koramangala, Bangalore'],
            ['Kavitha Pillai',      'student8@sims.test',  'STU008', 'ADM2025008', $mathY1, $mathDept, '2005-06-17', 'female', 'O+',  '23, Whitefield, Bangalore'],
            ['Siddharth Rao',       'student9@sims.test',  'STU009', 'ADM2025009', $mathY1, $mathDept, '2005-04-03', 'male',   'A+',  '67, HSR Layout, Bangalore'],
            ['Ananya Joshi',        'student10@sims.test', 'STU010', 'ADM2025010', $mathY1, $mathDept, '2005-12-20', 'female', 'B+',  '45, JP Nagar, Bangalore'],
            // Science Year 1
            ['Varun Malhotra',      'student11@sims.test', 'STU011', 'ADM2025011', $sciY1,  $sciDept,  '2005-08-30', 'male',   'AB-', '12, Anna Nagar, Chennai'],
            ['Lakshmi Venkatesh',   'student12@sims.test', 'STU012', 'ADM2025012', $sciY1,  $sciDept,  '2005-10-14', 'female', 'O+',  '34, T. Nagar, Chennai'],
            ['Nikhil Gupta',        'student13@sims.test', 'STU013', 'ADM2025013', $sciY1,  $sciDept,  '2005-03-07', 'male',   'A+',  '78, Adyar, Chennai'],
            ['Pooja Chauhan',       'student14@sims.test', 'STU014', 'ADM2025014', $sciY1,  $sciDept,  '2005-01-25', 'female', 'B+',  '90, Velachery, Chennai'],
            ['Rahul Desai',         'student15@sims.test', 'STU015', 'ADM2025015', $sciY1,  $sciDept,  '2005-07-09', 'male',   'O-',  '56, Porur, Chennai'],
        ];

        $stuRecs = [];
        foreach ($stuDefs as $i => $sd) {
            $u = $this->makeUser($sd[0], $sd[1], $studentRole);
            $stuRecs[] = Student::create([
                'user_id' => $u->id,
                'roll_number' => $sd[2],
                'admission_number' => $sd[3],
                'department_id' => $sd[5]->id,
                'college_class_id' => $sd[4]->id,
                'academic_year_id' => $ay->id,
                'date_of_birth' => $sd[6],
                'gender' => $sd[7],
                'blood_group' => $sd[8],
                'phone' => '+91 9'.str_pad((string) (876000000 + $i * 111), 9, '0', STR_PAD_LEFT),
                'address' => $sd[9],
                'admission_year' => 2025,
                'status' => 'active',
            ]);
        }

        // Guardian for STU001 (student@sims.test)
        $parentUser = $this->makeUser('Suresh Patel', 'parent@sims.test', $parentRole);
        Guardian::create([
            'student_id' => $stuRecs[0]->id,
            'user_id' => $parentUser->id,
            'first_name' => 'Suresh',
            'last_name' => 'Patel',
            'relation' => 'Father',
            'email' => 'parent@sims.test',
            'phone' => '+91 9876543299',
            'address' => 'Flat 201, Green Valley, Hyderabad',
            'is_primary_contact' => true,
        ]);

        $csStudents = array_slice($stuRecs, 0, 5); // STU001-STU005

        // ── 9. Subjects ─────────────────────────────────────────────────────
        $this->command->info('  → Subjects…');

        // CS Year 1 — 6 subjects
        $cs101 = Subject::create(['name' => 'Programming Fundamentals', 'code' => 'CS101', 'college_class_id' => $csY1->id,   'department_id' => $csDept->id,   'faculty_id' => $fac1->id, 'credits' => 4, 'subject_type' => 'theory',    'grading_type' => 'marks',     'is_active' => true]);
        $cs102 = Subject::create(['name' => 'Data Structures',          'code' => 'CS102', 'college_class_id' => $csY1->id,   'department_id' => $csDept->id,   'faculty_id' => $fac2->id, 'credits' => 4, 'subject_type' => 'theory',    'grading_type' => 'marks',     'is_active' => true]);
        $cs103 = Subject::create(['name' => 'Mathematics I',            'code' => 'CS103', 'college_class_id' => $csY1->id,   'department_id' => $csDept->id,   'faculty_id' => $fac3->id, 'credits' => 3, 'subject_type' => 'theory',    'grading_type' => 'marks',     'is_active' => true]);
        $cs104 = Subject::create(['name' => 'Computer Networks',        'code' => 'CS104', 'college_class_id' => $csY1->id,   'department_id' => $csDept->id,   'faculty_id' => $fac1->id, 'credits' => 3, 'subject_type' => 'theory',    'grading_type' => 'marks',     'is_active' => true]);
        $cs105 = Subject::create(['name' => 'Drawing & Design',         'code' => 'CS105', 'college_class_id' => $csY1->id,   'department_id' => $csDept->id,   'faculty_id' => $fac2->id, 'credits' => 2, 'subject_type' => 'elective',  'grading_type' => 'grade',     'is_active' => true]);
        $cs106 = Subject::create(['name' => 'Sports & Fitness',         'code' => 'CS106', 'college_class_id' => $csY1->id,   'department_id' => $csDept->id,   'faculty_id' => $fac4->id, 'credits' => 1, 'subject_type' => 'practical', 'grading_type' => 'pass_fail', 'is_active' => true]);

        // Math Year 1 — 4 subjects
        Subject::create(['name' => 'Calculus',          'code' => 'MATH101', 'college_class_id' => $mathY1->id, 'department_id' => $mathDept->id, 'faculty_id' => $fac3->id, 'credits' => 4, 'subject_type' => 'theory',    'grading_type' => 'marks',     'is_active' => true]);
        Subject::create(['name' => 'Algebra',           'code' => 'MATH102', 'college_class_id' => $mathY1->id, 'department_id' => $mathDept->id, 'faculty_id' => $fac3->id, 'credits' => 3, 'subject_type' => 'theory',    'grading_type' => 'marks',     'is_active' => true]);
        Subject::create(['name' => 'Statistics',        'code' => 'MATH103', 'college_class_id' => $mathY1->id, 'department_id' => $mathDept->id, 'faculty_id' => $fac3->id, 'credits' => 3, 'subject_type' => 'theory',    'grading_type' => 'marks',     'is_active' => true]);
        Subject::create(['name' => 'Physical Education', 'code' => 'MATH104', 'college_class_id' => $mathY1->id, 'department_id' => $mathDept->id, 'faculty_id' => $fac4->id, 'credits' => 1, 'subject_type' => 'practical', 'grading_type' => 'pass_fail', 'is_active' => true]);

        // Science Year 1 — 4 subjects
        Subject::create(['name' => 'Physics',   'code' => 'SCI101', 'college_class_id' => $sciY1->id, 'department_id' => $sciDept->id, 'faculty_id' => $fac4->id, 'credits' => 4, 'subject_type' => 'theory',    'grading_type' => 'marks', 'is_active' => true]);
        Subject::create(['name' => 'Chemistry', 'code' => 'SCI102', 'college_class_id' => $sciY1->id, 'department_id' => $sciDept->id, 'faculty_id' => $fac5->id, 'credits' => 4, 'subject_type' => 'theory',    'grading_type' => 'marks', 'is_active' => true]);
        Subject::create(['name' => 'Biology',   'code' => 'SCI103', 'college_class_id' => $sciY1->id, 'department_id' => $sciDept->id, 'faculty_id' => $fac4->id, 'credits' => 3, 'subject_type' => 'theory',    'grading_type' => 'marks', 'is_active' => true]);
        Subject::create(['name' => 'Lab Work',  'code' => 'SCI104', 'college_class_id' => $sciY1->id, 'department_id' => $sciDept->id, 'faculty_id' => $fac5->id, 'credits' => 2, 'subject_type' => 'practical', 'grading_type' => 'grade', 'is_active' => true]);

        // ── 10. Timetable slots (CS Year 1, Mon–Fri) ────────────────────────
        $this->command->info('  → Timetable slots…');
        $periods = [
            1 => [$cs101, $fac1, '09:00', '09:45'],
            2 => [$cs102, $fac2, '09:45', '10:30'],
            3 => [$cs103, $fac3, '10:45', '11:30'],
            4 => [$cs104, $fac1, '11:30', '12:15'],
            5 => [$cs105, $fac2, '13:00', '13:45'],
            6 => [$cs106, $fac4, '13:45', '14:30'],
        ];
        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day) {
            foreach ($periods as $pno => [$subj, $fac, $start, $end]) {
                TimetableSlot::create([
                    'college_class_id' => $csY1->id,
                    'subject_id' => $subj->id,
                    'faculty_id' => $fac->id,
                    'academic_year_id' => $ay->id,
                    'day_of_week' => $day,
                    'period_number' => $pno,
                    'start_time' => $start,
                    'end_time' => $end,
                    // legacy NOT NULL columns
                    'day' => ucfirst($day),
                    'period' => (string) $pno,
                ]);
            }
        }

        // ── 11. Grading levels ──────────────────────────────────────────────
        $this->command->info('  → Grading levels…');

        // Class-specific scholastic grades for CS Year 1
        $gl = [];
        foreach ([
            ['A1', 92, 100, 10.0], ['A2', 83, 91, 9.0],
            ['B1', 75, 82,  8.0], ['B2', 67, 74, 7.0],
            ['C1', 59, 66,  6.0], ['C2', 51, 58, 5.0],
            ['D1', 43, 50,  4.0], ['D2', 35, 42, 3.0],
            ['E',   0, 34,  0.0],
        ] as [$name, $min, $max, $gp]) {
            $gl[$name] = GradingLevel::create([
                'name' => $name,
                'min_score' => $min,
                'max_score' => $max,
                'grade_point' => $gp,
                'type' => 'scholastic',
                'academic_year_id' => $ay->id,
                'college_class_id' => $csY1->id,
            ]);
        }

        // Class-specific co-scholastic grades for CS Year 1
        foreach ([
            ['A+', 91, 100, 5.0], ['A', 71, 90, 4.0],
            ['B+', 51, 70,  3.0], ['B', 41, 50, 2.0],
            ['C',   0, 40,  1.0],
        ] as [$name, $min, $max, $gp]) {
            $gl[$name] = GradingLevel::create([
                'name' => $name,
                'min_score' => $min,
                'max_score' => $max,
                'grade_point' => $gp,
                'type' => 'co_scholastic',
                'academic_year_id' => $ay->id,
                'college_class_id' => $csY1->id,
            ]);
        }

        // Global scholastic fallback (no class_id) — used by Math/Sci students
        foreach ([
            ['A1', 92, 100, 10.0], ['A2', 83, 91, 9.0],
            ['B1', 75, 82,  8.0], ['B2', 67, 74, 7.0],
            ['C1', 59, 66,  6.0], ['C2', 51, 58, 5.0],
            ['D1', 43, 50,  4.0], ['D2', 35, 42, 3.0],
            ['E',   0, 34,  0.0],
        ] as [$name, $min, $max, $gp]) {
            GradingLevel::create([
                'name' => $name,
                'min_score' => $min,
                'max_score' => $max,
                'grade_point' => $gp,
                'type' => 'scholastic',
                'academic_year_id' => $ay->id,
                'college_class_id' => null,
            ]);
        }

        // Helper: look up grade level from a marks/max pair
        $grade = function (float $marks, float $max) use ($csY1, &$gl): ?GradingLevel {
            if ($max <= 0) {
                return null;
            }

            return GradingLevel::calculateGrade(round(($marks / $max) * 100, 2), $csY1->id);
        };

        // ── 12. Exam groups, exams, and scores ──────────────────────────────
        $this->command->info('  → Exams and scores…');

        $scholSubjects = [$cs101, $cs102, $cs103, $cs104];

        // ------------------------------------------------------------------
        // FA1 – Published  (4 tools × 5 marks each per subject)
        // Scores per tool: [stu1, stu2, stu3, stu4, stu5]
        // ------------------------------------------------------------------
        $fa1 = ExamGroup::create([
            'name' => 'FA1', 'type' => 'fa', 'college_class_id' => $csY1->id,
            'academic_year_id' => $ay->id, 'exam_type' => 'marks',
            'start_date' => '2025-07-10', 'end_date' => '2025-07-15',
            'conducted_date' => '2025-07-15', 'is_published' => true,
        ]);
        $this->seedFaTools($fa1, $scholSubjects, $csStudents, $admin, $grade, '2025-07-10', [
            $cs101->id => [[4, 5, 3, 2, 4], [5, 5, 4, 3, 4], [5, 4, 4, 2, 5], [4, 5, 3, 2, 3]],
            $cs102->id => [[3, 5, 4, 2, 4], [4, 5, 3, 3, 5], [5, 4, 3, 2, 4], [4, 5, 2, 2, 3]],
            $cs103->id => [[4, 4, 3, 2, 3], [5, 5, 4, 3, 4], [4, 4, 3, 2, 3], [4, 5, 3, 2, 4]],
            $cs104->id => [[3, 4, 3, 2, 4], [4, 5, 3, 2, 4], [5, 4, 4, 2, 3], [3, 4, 2, 2, 4]],
        ]);

        // ------------------------------------------------------------------
        // FA2 – Published
        // ------------------------------------------------------------------
        $fa2 = ExamGroup::create([
            'name' => 'FA2', 'type' => 'fa', 'college_class_id' => $csY1->id,
            'academic_year_id' => $ay->id, 'exam_type' => 'marks',
            'start_date' => '2025-09-05', 'end_date' => '2025-09-10',
            'conducted_date' => '2025-09-10', 'is_published' => true,
        ]);
        $this->seedFaTools($fa2, $scholSubjects, $csStudents, $admin, $grade, '2025-09-05', [
            $cs101->id => [[5, 5, 4, 3, 4], [4, 5, 3, 3, 5], [5, 5, 4, 3, 4], [4, 4, 3, 3, 4]],
            $cs102->id => [[4, 5, 3, 2, 4], [5, 5, 4, 3, 4], [4, 5, 3, 2, 3], [4, 5, 3, 3, 4]],
            $cs103->id => [[5, 5, 4, 3, 5], [4, 5, 3, 3, 4], [5, 5, 3, 3, 4], [5, 5, 4, 3, 4]],
            $cs104->id => [[4, 5, 3, 3, 4], [5, 5, 4, 3, 5], [4, 4, 3, 2, 4], [4, 5, 3, 3, 4]],
        ]);

        // ------------------------------------------------------------------
        // SA1 – Published  (single exam per subject, 50 marks)
        // ------------------------------------------------------------------
        $sa1 = ExamGroup::create([
            'name' => 'SA1', 'type' => 'sa', 'college_class_id' => $csY1->id,
            'academic_year_id' => $ay->id, 'exam_type' => 'marks',
            'start_date' => '2025-10-15', 'end_date' => '2025-10-20',
            'conducted_date' => '2025-10-20', 'is_published' => true,
        ]);
        $this->seedSaExams($sa1, $scholSubjects, $csStudents, $admin, $grade, [
            $cs101->id => ['2025-10-15', [38, 46, 28, 20, 35]],
            $cs102->id => ['2025-10-16', [35, 44, 25, 18, 33]],
            $cs103->id => ['2025-10-17', [40, 47, 30, 22, 38]],
            $cs104->id => ['2025-10-18', [36, 45, 26, 19, 34]],
        ], 50, 17);

        // ------------------------------------------------------------------
        // FA3 – Published
        // ------------------------------------------------------------------
        $fa3 = ExamGroup::create([
            'name' => 'FA3', 'type' => 'fa', 'college_class_id' => $csY1->id,
            'academic_year_id' => $ay->id, 'exam_type' => 'marks',
            'start_date' => '2025-12-10', 'end_date' => '2025-12-15',
            'conducted_date' => '2025-12-15', 'is_published' => true,
        ]);
        $this->seedFaTools($fa3, $scholSubjects, $csStudents, $admin, $grade, '2025-12-10', [
            $cs101->id => [[5, 5, 3, 3, 4], [4, 5, 4, 3, 4], [5, 5, 3, 2, 5], [4, 4, 3, 3, 4]],
            $cs102->id => [[4, 5, 3, 3, 5], [5, 5, 3, 2, 4], [4, 5, 3, 3, 4], [5, 5, 4, 3, 4]],
            $cs103->id => [[5, 5, 4, 3, 4], [4, 5, 3, 3, 5], [5, 4, 3, 3, 4], [4, 5, 4, 3, 4]],
            $cs104->id => [[4, 5, 3, 2, 4], [5, 5, 4, 3, 4], [4, 5, 3, 3, 5], [4, 4, 3, 3, 4]],
        ]);

        // ------------------------------------------------------------------
        // SA2 – NOT published  (conducted 2026-01-25)
        // ------------------------------------------------------------------
        $sa2 = ExamGroup::create([
            'name' => 'SA2', 'type' => 'sa', 'college_class_id' => $csY1->id,
            'academic_year_id' => $ay->id, 'exam_type' => 'marks',
            'start_date' => '2026-01-20', 'end_date' => '2026-01-25',
            'conducted_date' => '2026-01-25', 'is_published' => false,
        ]);
        $this->seedSaExams($sa2, $scholSubjects, $csStudents, $admin, $grade, [
            $cs101->id => ['2026-01-20', [42, 48, 30, 22, 38]],
            $cs102->id => ['2026-01-21', [38, 46, 27, 20, 35]],
            $cs103->id => ['2026-01-22', [44, 49, 32, 24, 40]],
            $cs104->id => ['2026-01-23', [40, 47, 28, 21, 36]],
        ], 50, 17);

        // ------------------------------------------------------------------
        // FA4 – NOT published  (conducted 2026-02-10)
        // ------------------------------------------------------------------
        $fa4 = ExamGroup::create([
            'name' => 'FA4', 'type' => 'fa', 'college_class_id' => $csY1->id,
            'academic_year_id' => $ay->id, 'exam_type' => 'marks',
            'start_date' => '2026-02-05', 'end_date' => '2026-02-10',
            'conducted_date' => '2026-02-10', 'is_published' => false,
        ]);
        $this->seedFaTools($fa4, $scholSubjects, $csStudents, $admin, $grade, '2026-02-05', [
            $cs101->id => [[5, 5, 4, 3, 5], [4, 5, 3, 3, 4], [5, 5, 4, 3, 4], [4, 5, 3, 3, 5]],
            $cs102->id => [[4, 5, 4, 3, 4], [5, 5, 3, 3, 5], [4, 5, 4, 3, 4], [5, 4, 3, 3, 4]],
            $cs103->id => [[5, 5, 3, 3, 4], [4, 5, 4, 3, 5], [5, 5, 3, 3, 4], [4, 5, 4, 3, 4]],
            $cs104->id => [[4, 5, 3, 3, 5], [5, 5, 4, 3, 4], [4, 5, 3, 3, 4], [5, 5, 4, 3, 5]],
        ]);

        // ── 13. Attendance ──────────────────────────────────────────────────
        $this->command->info('  → Attendance (this may take a moment)…');
        // Target percentages for STU001–STU005
        $targets = [85, 92, 68, 55, 78];
        $reasons = ['Medical leave', 'Family emergency', 'Out of station', 'Fever', 'Personal reasons'];

        // Build ordered list of weekdays from 2025-06-01 → today
        $workingDays = [];
        $cur = Carbon::parse('2025-06-01');
        $today = Carbon::today();
        while ($cur->lte($today)) {
            if (! in_array($cur->dayOfWeek, [0, 6])) {   // skip Sun/Sat
                $workingDays[] = $cur->toDateString();
            }
            $cur->addDay();
        }
        $total = count($workingDays);

        foreach ($csStudents as $idx => $stu) {
            $presentCount = (int) round($total * $targets[$idx] / 100);
            $absentCount = $total - $presentCount;

            // Deterministic shuffle using student index as seed
            $indices = range(0, $total - 1);
            srand(1234 + $idx);
            shuffle($indices);
            $absentSet = array_flip(array_slice($indices, 0, $absentCount));

            foreach ($workingDays as $dayIdx => $date) {
                if (isset($absentSet[$dayIdx])) {
                    // 60% absent, 25% late, 15% excused
                    $r = ($dayIdx + $idx * 7) % 100;
                    $status = $r < 60 ? 'absent' : ($r < 85 ? 'late' : 'excused');
                    $notes = $reasons[($dayIdx + $idx) % count($reasons)];
                } else {
                    $status = 'present';
                    $notes = null;
                }
                DB::table('attendances')->insert([
                    'student_id' => $stu->id,
                    'college_class_id' => $csY1->id,
                    'attendance_date' => $date,
                    'status' => $status,
                    'notes' => $notes,
                    'marked_by' => $admin->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ── 14. Fee structures + payments ───────────────────────────────────
        $this->command->info('  → Fees…');
        $catTuition = FeeCategory::create(['name' => 'Tuition Fee', 'academic_year_id' => $ay->id, 'description' => 'Quarterly tuition fee']);
        $catExam = FeeCategory::create(['name' => 'Exam Fee',    'academic_year_id' => $ay->id, 'description' => 'Annual examination fee']);
        $catLib = FeeCategory::create(['name' => 'Library Fee', 'academic_year_id' => $ay->id, 'description' => 'Annual library access fee']);
        $catSports = FeeCategory::create(['name' => 'Sports Fee',  'academic_year_id' => $ay->id, 'description' => 'Annual sports facilities fee']);

        $fsTuitionQ1 = FeeStructure::create(['college_class_id' => $csY1->id, 'fee_category_id' => $catTuition->id, 'academic_year_id' => $ay->id, 'amount' => 15000.00, 'due_date' => '2025-07-10', 'frequency' => 'quarterly']);
        $fsTuitionQ2 = FeeStructure::create(['college_class_id' => $csY1->id, 'fee_category_id' => $catTuition->id, 'academic_year_id' => $ay->id, 'amount' => 15000.00, 'due_date' => '2025-10-10', 'frequency' => 'quarterly']);
        $fsExam = FeeStructure::create(['college_class_id' => $csY1->id, 'fee_category_id' => $catExam->id,    'academic_year_id' => $ay->id, 'amount' => 1500.00, 'due_date' => '2025-08-01', 'frequency' => 'annually']);
        $fsLib = FeeStructure::create(['college_class_id' => $csY1->id, 'fee_category_id' => $catLib->id,     'academic_year_id' => $ay->id, 'amount' => 500.00, 'due_date' => '2025-08-01', 'frequency' => 'annually']);
        $fsSports = FeeStructure::create(['college_class_id' => $csY1->id, 'fee_category_id' => $catSports->id,  'academic_year_id' => $ay->id, 'amount' => 800.00, 'due_date' => '2025-08-01', 'frequency' => 'annually']);

        $stu001 = $csStudents[0];
        $payDefs = [
            [$fsTuitionQ1, 15000.00, '2025-07-05', 'cash',   'RCPT-2025-00001', 'Q1 Tuition – paid in full (cash)'],
            [$fsTuitionQ2, 15000.00, '2025-10-08', 'online', 'RCPT-2025-00002', 'Q2 Tuition – paid via net banking'],
            [$fsExam,       1500.00, '2025-07-20', 'cash',   'RCPT-2025-00003', 'Annual Exam Fee – paid'],
            [$fsLib,         500.00, '2025-07-20', 'online', 'RCPT-2025-00004', 'Library Fee – paid online'],
            [$fsSports,      400.00, '2025-07-25', 'cash',   'RCPT-2025-00005', 'Sports Fee – partial payment (₹400 of ₹800)'],
        ];
        foreach ($payDefs as [$fs, $amt, $dt, $mode, $rcpt, $note]) {
            FeePayment::create([
                'student_id' => $stu001->id,
                'fee_structure_id' => $fs->id,
                'amount_paid' => $amt,
                'payment_date' => $dt,
                'payment_mode' => $mode,
                'receipt_number' => $rcpt,
                'fine_amount' => 0,
                'tax_amount' => 0,
                'collected_by' => $admin->id,
                'notes' => $note,
            ]);
        }

        // ── 15. Notices ─────────────────────────────────────────────────────
        $this->command->info('  → Notices…');
        $noticeDefs = [
            ['Welcome to Academic Year 2025-26',
                'Dear Students and Parents, we are delighted to welcome you to the academic year 2025-26. This year promises to be full of learning, growth, and exciting opportunities. Please go through the academic calendar and fee schedule available at the front office. We look forward to a productive and rewarding year.',
                'all', null, '2025-06-01 08:00:00'],
            ['FA1 Results Published',
                'The results for Formative Assessment 1 (FA1) have been published on the student portal. Students may log in to view their subject-wise scores and grades. Any discrepancy must be reported to the respective subject teacher within 3 working days of this notice. Re-evaluation requests will not be entertained after the deadline.',
                'student', null, '2025-07-20 10:00:00'],
            ['Fee Payment Reminder – Q3 Instalment',
                'This is a formal reminder that the Q3 tuition fee instalment of ₹15,000 is due by January 10, 2026. Students with outstanding dues will not be permitted to appear for the SA2 examinations. Please visit the Accounts Department between 9 AM and 3 PM on any working day to clear your dues. Online payments via the portal are also accepted.',
                'all', null, '2025-12-20 09:00:00'],
            ['Annual Sports Day – December 15, 2025',
                'The Annual Sports Day will be held on December 15, 2025 on the college grounds. Events include 100m/200m sprint, long jump, shot put, tug-of-war, and cricket. Students wishing to participate must register at the Sports Office by December 5, 2025. Parents and guardians are cordially invited.',
                'all', null, '2025-11-25 11:00:00'],
            ['CS Department Seminar – Emerging Trends in Computing',
                'A special seminar on "Emerging Trends in Computer Science: AI, Cloud, and Quantum Computing" will be held on November 10, 2025 from 2:00 PM to 5:00 PM in the CS Conference Hall (Room 301). Guest Speaker: Dr. Ramesh Kumar (IIT Hyderabad). Attendance is compulsory for all CS Year 1 students. Please carry your college ID.',
                'student', $csY1->id, '2025-11-01 09:00:00'],
        ];
        foreach ($noticeDefs as [$title, $body, $target, $classId, $pubAt]) {
            Notice::create([
                'title' => $title,
                'body' => $body,
                'target' => $target,
                'college_class_id' => $classId,
                'created_by' => $admin->id,
                'published_at' => $pubAt,
            ]);
        }

        // ── 16. Events ──────────────────────────────────────────────────────
        $this->command->info('  → Events…');
        $eventDefs = [
            ['Annual Sports Day',  'sports',   '2025-12-15', 'Annual inter-class and inter-department sports competition featuring track events, team sports, and individual challenges. Prizes will be awarded to top performers in each category.'],
            ['Science Exhibition', 'academic', '2026-01-20', 'Annual science exhibition showcasing innovative student projects and research findings. Open to all departments. Top 3 projects receive cash prizes and certificates. Registration deadline: January 10.'],
            ['Cultural Festival',  'cultural', '2026-02-10', 'Two-day cultural extravaganza celebrating the rich diversity of our student community through music, classical dance, drama, poetry recitation, and visual arts. All students, staff, and parents are welcome.'],
        ];
        foreach ($eventDefs as [$title, $type, $date, $desc]) {
            Event::create([
                'title' => $title,
                'description' => $desc,
                'event_type' => $type,
                'event_date' => $date,
                'academic_year_id' => $ay->id,
                'target' => 'all',
                'college_class_id' => null,
                'created_by' => $admin->id,
            ]);
        }

        // ── 17. Enquiries + Admissions ──────────────────────────────────────
        $this->command->info('  → Enquiries & Admissions…');
        $enq1 = Enquiry::create(['student_name' => 'Rahul Sharma', 'parent_name' => 'Vikram Sharma',  'phone' => '+91 9876001234', 'email' => 'rahul.sharma@email.com',  'applying_for_class_id' => $csY1->id,   'academic_year_id' => $ay->id, 'source' => 'walkin',  'status' => 'new',       'enquiry_date' => '2025-05-10', 'notes' => 'Walk-in enquiry. Strong interest in CS. Academic record looks good.']);
        $enq2 = Enquiry::create(['student_name' => 'Priya Patel',  'parent_name' => 'Mahesh Patel',   'phone' => '+91 9876002345', 'email' => 'priya.patel@email.com',   'applying_for_class_id' => $mathY1->id, 'academic_year_id' => $ay->id, 'source' => 'referral', 'status' => 'followup', 'enquiry_date' => '2025-05-15', 'notes' => 'Referred by current student Arjun Patel (STU001). Interested in Mathematics. Follow-up call scheduled for May 22.']);
        $enq3 = Enquiry::create(['student_name' => 'Amit Kumar',   'parent_name' => 'Sunil Kumar',    'phone' => '+91 9876003456', 'email' => 'amit.kumar@email.com',    'applying_for_class_id' => $csY1->id,   'academic_year_id' => $ay->id, 'source' => 'online',  'status' => 'converted', 'enquiry_date' => '2025-05-20', 'notes' => 'Applied via website. Documents verified. Converted to admission on May 25.']);

        Admission::create([
            'enquiry_id' => $enq3->id,
            'student_name' => 'Amit Kumar',
            'date_of_birth' => '2005-09-15',
            'gender' => 'male',
            'parent_name' => 'Sunil Kumar',
            'phone' => '+91 9876003456',
            'email' => 'amit.kumar@email.com',
            'address' => 'Plot 12, Secunderabad, Telangana',
            'previous_school' => 'Delhi Public School, Hyderabad',
            'previous_class' => 'Class 10',
            'applying_for_class_id' => $csY1->id,
            'academic_year_id' => $ay->id,
            'status' => 'approved',
            'admission_date' => '2025-05-25',
            'documents_submitted' => ['birth_certificate', 'transfer_certificate', 'report_card'],
            'remarks' => 'All documents verified. Admission approved. Fee payment pending.',
        ]);

        Admission::create([
            'enquiry_id' => null,
            'student_name' => 'Pooja Singh',
            'date_of_birth' => '2005-04-20',
            'gender' => 'female',
            'parent_name' => 'Rajesh Singh',
            'phone' => '+91 9876004567',
            'email' => 'pooja.singh@email.com',
            'address' => 'H.No 88, Kondapur, Hyderabad',
            'previous_school' => 'Kendriya Vidyalaya No. 1, Hyderabad',
            'previous_class' => 'Class 10',
            'applying_for_class_id' => $sciY1->id,
            'academic_year_id' => $ay->id,
            'status' => 'enrolled',
            'admission_date' => '2025-05-28',
            'documents_submitted' => ['birth_certificate', 'transfer_certificate', 'report_card', 'character_certificate'],
            'remarks' => 'All documents verified. Enrolled. Fee paid in full.',
        ]);

        // ── 18. Lesson plans ────────────────────────────────────────────────
        $this->command->info('  → Lesson plans…');
        $lessonDefs = [
            ['2025-06-02', 'Introduction to Programming',
                'Overview of programming concepts, history of programming languages, and introduction to Python. Topics: algorithms, flowcharts, pseudocode.',
                'Use interactive examples. Assign Hello World exercise in Python.',
                'completed'],
            ['2025-06-09', 'Variables and Data Types',
                'Understanding variables, constants, and primitive data types (int, float, string, bool). Type casting and type conversion. Mutable vs immutable types.',
                'Lab session: Practice type conversion exercises. Quiz at end of week.',
                'completed'],
            ['2025-06-16', 'Control Structures',
                'Conditional statements (if/elif/else), loops (for, while), break and continue statements, nested loops, and loop control flow.',
                'Assignment: FizzBuzz problem and number pattern programs (due next Friday).',
                'planned'],
        ];
        foreach ($lessonDefs as [$weekStart, $topic, $desc, $notes, $status]) {
            LessonPlan::create([
                'faculty_id' => $fac1->id,
                'subject_id' => $cs101->id,
                'college_class_id' => $csY1->id,
                'academic_year_id' => $ay->id,
                'week_start_date' => $weekStart,
                'topic' => $topic,
                'description' => $desc,
                'notes' => $notes,
                'status' => $status,
            ]);
        }

        // ── Done ────────────────────────────────────────────────────────────
        $this->command->newLine();
        $this->command->info('✅ Seed complete. Summary:');
        $this->command->table(
            ['Table', 'Rows'],
            [
                ['school_settings',  DB::table('school_settings')->count()],
                ['academic_years',   DB::table('academic_years')->count()],
                ['departments',      DB::table('departments')->count()],
                ['college_classes',  DB::table('college_classes')->count()],
                ['users',            DB::table('users')->count()],
                ['faculties',        DB::table('faculties')->count()],
                ['students',         DB::table('students')->count()],
                ['guardians',        DB::table('guardians')->count()],
                ['subjects',         DB::table('subjects')->count()],
                ['timetable_slots',  DB::table('timetable_slots')->count()],
                ['grading_levels',   DB::table('grading_levels')->count()],
                ['exam_groups',      DB::table('exam_groups')->count()],
                ['exams',            DB::table('exams')->count()],
                ['exam_scores',      DB::table('exam_scores')->count()],
                ['attendances',      DB::table('attendances')->count()],
                ['fee_categories',   DB::table('fee_categories')->count()],
                ['fee_structures',   DB::table('fee_structures')->count()],
                ['fee_payments',     DB::table('fee_payments')->count()],
                ['notices',          DB::table('notices')->count()],
                ['events',           DB::table('events')->count()],
                ['enquiries',        DB::table('enquiries')->count()],
                ['admissions',       DB::table('admissions')->count()],
                ['lesson_plans',     DB::table('lesson_plans')->count()],
            ]
        );
        $this->command->newLine();
        $this->command->line('  <comment>Login credentials (all passwords: password)</comment>');
        $this->command->table(
            ['Role', 'Email'],
            [
                ['Admin',    'admin@sims.test'],
                ['Faculty',  'faculty@sims.test … faculty5@sims.test'],
                ['Student',  'student@sims.test (STU001) … student15@sims.test'],
                ['Parent',   'parent@sims.test (guardian of Arjun Patel)'],
            ]
        );
    }

    // --------------------------------------------------------------------------
    // Helpers
    // --------------------------------------------------------------------------

    /** Truncate all application data tables (FK constraints disabled). */
    private function wipe(): void
    {
        $this->command->info('  → Wiping existing data…');
        Schema::disableForeignKeyConstraints();
        $tables = [
            'lesson_plans', 'event_participants', 'events',
            'admissions', 'enquiries',
            'fee_payments', 'fee_discounts', 'fee_structures', 'fee_categories',
            'exam_scores', 'exams', 'exam_groups',
            'grading_levels', 'attendances', 'timetable_slots', 'grades',
            'subjects', 'guardians', 'students', 'faculties',
            'model_has_roles', 'users',
            'notices', 'college_classes', 'departments',
            'academic_years', 'school_settings',
        ];
        foreach ($tables as $t) {
            if (Schema::hasTable($t)) {
                DB::table($t)->truncate();
            }
        }
        Schema::enableForeignKeyConstraints();
    }

    /** Create a User and assign a role. */
    private function makeUser(string $name, string $email, Role $role): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole($role);

        return $user;
    }

    /** Create a CollegeClass linked to the academic year. */
    private function makeClass(string $name, Department $dept, int $semester, AcademicYear $ay): CollegeClass
    {
        return CollegeClass::create([
            'name' => $name,
            'department_id' => $dept->id,
            'section' => 'A',
            'semester' => $semester,
            'academic_year' => $ay->start_year,
            'academic_year_id' => $ay->id,
        ]);
    }

    /**
     * Seed FA-style exam tools (multiple exams per subject, each out of 5 marks).
     *
     * @param  array<int, array<int, array<int, int>>>  $toolsMap  subject_id → [tool0scores, tool1scores, …]
     * @param  callable  $grade  resolves marks+max → GradingLevel|null
     */
    private function seedFaTools(
        ExamGroup $group,
        array $subjects,
        array $students,
        User $enteredBy,
        callable $grade,
        string $baseDate,
        array $toolsMap
    ): void {
        foreach ($subjects as $subject) {
            $toolScores = $toolsMap[$subject->id] ?? [];
            foreach ($toolScores as $toolIdx => $stuScores) {
                $date = Carbon::parse($baseDate)->addDays($toolIdx)->toDateString();
                $exam = Exam::create([
                    'exam_group_id' => $group->id,
                    'subject_id' => $subject->id,
                    'date' => $date,
                    'maximum_marks' => 5,
                    'minimum_marks' => 2,
                    'weightage' => 1,
                ]);
                foreach ($students as $stuIdx => $stu) {
                    $marks = $stuScores[$stuIdx] ?? 0;
                    ExamScore::create([
                        'exam_id' => $exam->id,
                        'student_id' => $stu->id,
                        'marks_obtained' => $marks,
                        'grading_level_id' => $grade($marks, 5)?->id,
                        'absent' => false,
                        'entered_by' => $enteredBy->id,
                    ]);
                }
            }
        }
    }

    /**
     * Seed SA-style exams (one exam per subject).
     *
     * @param  array<int, array{string, array<int, int>}>  $scoreMap  subject_id → [date, [stu0, …]]
     */
    private function seedSaExams(
        ExamGroup $group,
        array $subjects,
        array $students,
        User $enteredBy,
        callable $grade,
        array $scoreMap,
        float $maxMarks,
        float $minMarks
    ): void {
        foreach ($subjects as $subject) {
            [$date, $stuScores] = $scoreMap[$subject->id] ?? [now()->toDateString(), []];
            $exam = Exam::create([
                'exam_group_id' => $group->id,
                'subject_id' => $subject->id,
                'date' => $date,
                'maximum_marks' => $maxMarks,
                'minimum_marks' => $minMarks,
                'weightage' => 1,
            ]);
            foreach ($students as $stuIdx => $stu) {
                $marks = $stuScores[$stuIdx] ?? 0;
                ExamScore::create([
                    'exam_id' => $exam->id,
                    'student_id' => $stu->id,
                    'marks_obtained' => $marks,
                    'grading_level_id' => $grade($marks, $maxMarks)?->id,
                    'absent' => false,
                    'entered_by' => $enteredBy->id,
                ]);
            }
        }
    }
}

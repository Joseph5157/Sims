<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\CollegeClass;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Notice;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Tenant database seeder.
     *
     * This seeds institution-scoped demo data, including a tenant admin user
     * that can log into the Filament admin panel.
     */
    public function run(): void
    {
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
            '--option' => 'permissions',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $facultyRole = Role::firstOrCreate(['name' => 'faculty', 'guard_name' => 'web']);
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $adminUser = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $adminUser->syncRoles([$adminRole]);

        $facultyUser = User::query()->firstOrCreate(
            ['email' => 'faculty@example.com'],
            [
                'name' => 'Faculty User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $facultyUser->syncRoles([$facultyRole]);

        $studentUser = User::query()->firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $studentUser->syncRoles([$studentRole]);

        $adminRole->syncPermissions(Permission::all());

        // Departments
        $csDept = Department::firstOrCreate(
            ['name' => 'Computer Science'],
            [
                'code' => 'CS',
                'description' => 'Computer Science Department',
            ]
        );

        $mathDept = Department::firstOrCreate(
            ['name' => 'Mathematics'],
            [
                'code' => 'MATH',
                'description' => 'Mathematics Department',
            ]
        );

        // College Classes
        $csYear1 = CollegeClass::firstOrCreate(
            ['name' => 'CS Year 1'],
            [
                'department_id' => $csDept->id,
                'section' => 'A',
                'semester' => 1,
                'academic_year' => 2023,
            ]
        );

        $csYear2 = CollegeClass::firstOrCreate(
            ['name' => 'CS Year 2'],
            [
                'department_id' => $csDept->id,
                'section' => 'A',
                'semester' => 3,
                'academic_year' => 2023,
            ]
        );

        $mathYear1 = CollegeClass::firstOrCreate(
            ['name' => 'Math Year 1'],
            [
                'department_id' => $mathDept->id,
                'section' => 'A',
                'semester' => 1,
                'academic_year' => 2023,
            ]
        );

        $mathYear2 = CollegeClass::firstOrCreate(
            ['name' => 'Math Year 2'],
            [
                'department_id' => $mathDept->id,
                'section' => 'A',
                'semester' => 3,
                'academic_year' => 2023,
            ]
        );

        // Additional Faculty User and Faculty Records
        $facultyUser2 = User::query()->firstOrCreate(
            ['email' => 'faculty2@example.com'],
            [
                'name' => 'Faculty User 2',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $facultyUser2->syncRoles([$facultyRole]);

        Faculty::firstOrCreate(
            ['employee_id' => 'FAC001'],
            [
                'user_id' => $facultyUser->id,
                'department_id' => $csDept->id,
                'qualification' => 'PhD in Computer Science',
                'joining_date' => '2020-08-15',
                'phone' => '123-456-7890',
                'specialization' => 'Algorithms',
            ]
        );

        Faculty::firstOrCreate(
            ['employee_id' => 'FAC002'],
            [
                'user_id' => $facultyUser2->id,
                'department_id' => $mathDept->id,
                'qualification' => 'PhD in Mathematics',
                'joining_date' => '2019-06-10',
                'phone' => '123-456-7891',
                'specialization' => 'Applied Mathematics',
            ]
        );

        // Additional Student Users and Student Records
        for ($i = 1; $i <= 5; $i++) {
            $studentEmail = "student{$i}@example.com";

            if ($i === 1) {
                $newStudentUser = $studentUser; // Reuse the first student user
            } else {
                $newStudentUser = User::query()->firstOrCreate(
                    ['email' => $studentEmail],
                    [
                        'name' => "Student User {$i}",
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                    ],
                );
                $newStudentUser->syncRoles([$studentRole]);
            }

            $classId = $i <= 3 ? $csYear1->id : $mathYear1->id;
            $deptId = $i <= 3 ? $csDept->id : $mathDept->id;

            Student::firstOrCreate(
                ['roll_number' => "STU00{$i}"],
                [
                    'user_id' => $newStudentUser->id,
                    'department_id' => $deptId,
                    'college_class_id' => $classId,
                    'date_of_birth' => '2000-01-01',
                    'phone' => "123-456-78" . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                    'address' => "Student Address {$i}",
                    'admission_year' => 2023,
                ]
            );
        }

        // Subjects
        $progFund = Subject::firstOrCreate(
            ['code' => 'CS101'],
            [
                'name' => 'Programming Fundamentals',
                'college_class_id' => $csYear1->id,
                'department_id' => $csDept->id,
                'credits' => 3,
            ]
        );

        Subject::firstOrCreate(
            ['code' => 'CS201'],
            [
                'name' => 'Data Structures',
                'college_class_id' => $csYear2->id,
                'department_id' => $csDept->id,
                'credits' => 3,
            ]
        );

        Subject::firstOrCreate(
            ['code' => 'MATH101'],
            [
                'name' => 'Calculus',
                'college_class_id' => $mathYear1->id,
                'department_id' => $mathDept->id,
                'credits' => 3,
            ]
        );

        // Grades + attendance + notices for the first student
        $firstStudent = Student::where('roll_number', 'STU001')->first();

        if ($firstStudent) {
            Grade::firstOrCreate(
                [
                    'student_id' => $firstStudent->id,
                    'subject_id' => $progFund->id,
                    'exam_type' => 'mid_term',
                ],
                [
                    'marks_obtained' => 75,
                    'total_marks' => 100,
                    'entered_by' => $adminUser->id,
                ]
            );

            Grade::firstOrCreate(
                [
                    'student_id' => $firstStudent->id,
                    'subject_id' => $progFund->id,
                    'exam_type' => 'final_exam',
                ],
                [
                    'marks_obtained' => 82,
                    'total_marks' => 100,
                    'entered_by' => $adminUser->id,
                ]
            );

            Grade::firstOrCreate(
                [
                    'student_id' => $firstStudent->id,
                    'subject_id' => $progFund->id,
                    'exam_type' => 'quiz',
                ],
                [
                    'marks_obtained' => 18,
                    'total_marks' => 20,
                    'entered_by' => $adminUser->id,
                ]
            );
        }

        Notice::firstOrCreate(
            [
                'title' => 'CS Department Meeting',
                'department_id' => $csDept->id,
            ],
            [
                'content' => 'There will be a department meeting for all CS students on next Friday at 2 PM in Room 301.',
                'created_by' => $adminUser->id,
                'expires_at' => now()->addDays(7),
            ]
        );

        Notice::firstOrCreate(
            [
                'title' => 'Project Submission Deadline',
                'department_id' => $csDept->id,
            ],
            [
                'content' => 'Reminder: Programming Fundamentals project submissions are due by end of this month.',
                'created_by' => $adminUser->id,
                'expires_at' => now()->addDays(30),
            ]
        );

        if ($firstStudent) {
            Attendance::firstOrCreate(
                [
                    'student_id' => $firstStudent->id,
                    'college_class_id' => $csYear1->id,
                    'attendance_date' => now()->subDays(1),
                ],
                [
                    'status' => 'present',
                    'notes' => 'Student attended class',
                    'marked_by' => $adminUser->id,
                ]
            );

            Attendance::firstOrCreate(
                [
                    'student_id' => $firstStudent->id,
                    'college_class_id' => $csYear1->id,
                    'attendance_date' => now()->subDays(2),
                ],
                [
                    'status' => 'present',
                    'notes' => 'Student attended class',
                    'marked_by' => $adminUser->id,
                ]
            );

            Attendance::firstOrCreate(
                [
                    'student_id' => $firstStudent->id,
                    'college_class_id' => $csYear1->id,
                    'attendance_date' => now()->subDays(3),
                ],
                [
                    'status' => 'absent',
                    'notes' => 'Student did not attend class',
                    'marked_by' => $adminUser->id,
                ]
            );
        }
    }
}

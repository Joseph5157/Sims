<?php

use App\Models\Attendance;
use App\Models\CollegeClass;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Carbon;

beforeEach(function (): void {
    $this->artisan('migrate:fresh', [
        '--path' => database_path('migrations/tenant'),
        '--realpath' => true,
    ]);
});

test('attendance percentage treats late and excused as present', function (): void {
    $user = User::factory()->create();
    $department = Department::create([
        'name' => 'Computer Science',
        'code' => 'CSE',
        'description' => null,
    ]);
    $collegeClass = CollegeClass::create([
        'name' => 'BSc CS',
        'section' => 'A',
        'department_id' => $department->id,
        'semester' => 1,
        'academic_year' => 2026,
    ]);

    $student = Student::create([
        'user_id' => $user->id,
        'roll_number' => 'R001',
        'department_id' => $department->id,
        'college_class_id' => $collegeClass->id,
        'date_of_birth' => null,
        'phone' => null,
        'address' => null,
        'admission_year' => 2026,
    ]);

    Attendance::create([
        'student_id' => $student->id,
        'college_class_id' => $collegeClass->id,
        'attendance_date' => Carbon::parse('2026-04-01')->toDateString(),
        'status' => 'present',
        'notes' => null,
        'marked_by' => null,
    ]);
    Attendance::create([
        'student_id' => $student->id,
        'college_class_id' => $collegeClass->id,
        'attendance_date' => Carbon::parse('2026-04-02')->toDateString(),
        'status' => 'absent',
        'notes' => null,
        'marked_by' => null,
    ]);
    Attendance::create([
        'student_id' => $student->id,
        'college_class_id' => $collegeClass->id,
        'attendance_date' => Carbon::parse('2026-04-03')->toDateString(),
        'status' => 'late',
        'notes' => null,
        'marked_by' => null,
    ]);
    Attendance::create([
        'student_id' => $student->id,
        'college_class_id' => $collegeClass->id,
        'attendance_date' => Carbon::parse('2026-04-04')->toDateString(),
        'status' => 'excused',
        'notes' => null,
        'marked_by' => null,
    ]);

    expect($student->getAttendancePercentage())->toBe(75.0);
});

test('attendance percentage is 0 when no attendance is marked', function (): void {
    $user = User::factory()->create();
    $department = Department::create([
        'name' => 'Physics',
        'code' => 'PHY',
        'description' => null,
    ]);
    $collegeClass = CollegeClass::create([
        'name' => 'BSc PHY',
        'section' => 'B',
        'department_id' => $department->id,
        'semester' => 1,
        'academic_year' => 2026,
    ]);

    $student = Student::create([
        'user_id' => $user->id,
        'roll_number' => 'R002',
        'department_id' => $department->id,
        'college_class_id' => $collegeClass->id,
        'date_of_birth' => null,
        'phone' => null,
        'address' => null,
        'admission_year' => 2026,
    ]);

    expect($student->getAttendancePercentage())->toBe(0.0);
});

<?php

namespace App\Imports;

use App\Models\CollegeClass;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Permission\Models\Role;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public array $imported = [];

    public array $skipped = [];

    public array $errors = [];

    public function collection(Collection $rows)
    {
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        foreach ($rows as $row) {
            try {
                // Validate required fields
                if (empty($row['email']) || empty($row['name']) || empty($row['roll_number'])) {
                    $this->errors[] = 'Row missing required fields (email, name, or roll_number)';

                    continue;
                }

                // Check if email already exists
                if (User::where('email', $row['email'])->exists()) {
                    $this->skipped[] = [
                        'email' => $row['email'],
                        'reason' => 'Email already exists',
                    ];

                    continue;
                }

                // Find department by code
                $department = Department::where('code', $row['department_code'])->first();
                if (! $department) {
                    $this->errors[] = "Department code '{$row['department_code']}' not found for email {$row['email']}";

                    continue;
                }

                // Find college class by name
                $collegeClass = CollegeClass::where('name', $row['class_name'])->first();
                if (! $collegeClass) {
                    $this->errors[] = "Class name '{$row['class_name']}' not found for email {$row['email']}";

                    continue;
                }

                // Create User
                $user = User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => bcrypt(bin2hex(random_bytes(16))),
                    'email_verified_at' => now(),
                ]);

                // Assign student role
                $user->syncRoles([$studentRole]);

                // Create Student record
                Student::create([
                    'user_id' => $user->id,
                    'roll_number' => $row['roll_number'],
                    'department_id' => $department->id,
                    'college_class_id' => $collegeClass->id,
                    'date_of_birth' => ! empty($row['date_of_birth']) ? $row['date_of_birth'] : null,
                    'phone' => $row['phone'] ?? null,
                    'address' => $row['address'] ?? null,
                    'admission_year' => (int) ($row['admission_year'] ?? date('Y')),
                ]);

                $this->imported[] = [
                    'email' => $row['email'],
                    'name' => $row['name'],
                    'roll_number' => $row['roll_number'],
                ];
            } catch (\Exception $e) {
                $this->errors[] = "Row {$row['email']}: {$e->getMessage()}";
            }
        }
    }
}

<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Permission\Models\Role;

class FacultyImport implements ToCollection, WithHeadingRow
{
    public array $imported = [];

    public array $skipped = [];

    public array $errors = [];

    public function collection(Collection $rows)
    {
        $facultyRole = Role::firstOrCreate(['name' => 'faculty', 'guard_name' => 'web']);

        foreach ($rows as $row) {
            try {
                // Validate required fields
                if (empty($row['email']) || empty($row['name']) || empty($row['employee_id'])) {
                    $this->errors[] = 'Row missing required fields (email, name, or employee_id)';

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

                // Check if employee_id already exists
                if (Faculty::where('employee_id', $row['employee_id'])->exists()) {
                    $this->skipped[] = [
                        'email' => $row['email'],
                        'reason' => 'Employee ID already exists',
                    ];

                    continue;
                }

                // Find department by code
                $department = Department::where('code', $row['department_code'])->first();
                if (! $department) {
                    $this->errors[] = "Department code '{$row['department_code']}' not found for email {$row['email']}";

                    continue;
                }

                // Create User
                $user = User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => bcrypt(bin2hex(random_bytes(16))),
                    'email_verified_at' => now(),
                ]);

                // Assign faculty role
                $user->syncRoles([$facultyRole]);

                // Create Faculty record
                Faculty::create([
                    'user_id' => $user->id,
                    'employee_id' => $row['employee_id'],
                    'department_id' => $department->id,
                    'qualification' => $row['qualification'] ?? null,
                    'phone' => $row['phone'] ?? null,
                    'specialization' => $row['specialization'] ?? null,
                    'joining_date' => ! empty($row['joining_date']) ? $row['joining_date'] : null,
                ]);

                $this->imported[] = [
                    'email' => $row['email'],
                    'name' => $row['name'],
                    'employee_id' => $row['employee_id'],
                ];
            } catch (\Exception $e) {
                $this->errors[] = "Row {$row['email']}: {$e->getMessage()}";
            }
        }
    }
}

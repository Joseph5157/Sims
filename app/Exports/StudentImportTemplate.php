<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentImportTemplate implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'John Doe',
                'john.doe@example.com',
                'STU001',
                'CS',
                'CS Year 1',
                '2000-01-15',
                '555-1234',
                '123 Main Street, City',
                '2026',
            ],
            [
                'Jane Smith',
                'jane.smith@example.com',
                'STU002',
                'CS',
                'CS Year 1',
                '2001-05-20',
                '555-5678',
                '456 Oak Avenue, City',
                '2026',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'email',
            'roll_number',
            'department_code',
            'class_name',
            'date_of_birth',
            'phone',
            'address',
            'admission_year',
        ];
    }
}

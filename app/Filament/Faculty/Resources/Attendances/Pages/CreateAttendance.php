<?php

namespace App\Filament\Faculty\Resources\Attendances\Pages;

use App\Filament\Faculty\Resources\AttendanceResource;
use App\Models\Attendance;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function handleRecordCreation(array $data): Attendance
    {
        return DB::transaction(function () use ($data) {
            $facultyId = Auth::id();
            $createdRecords = [];

            foreach ($data['students'] as $studentData) {
                $record = Attendance::updateOrCreate(
                    [
                        'student_id' => $studentData['student_id'],
                        'subject_id' => $data['subject_id'],
                        'date' => $data['date'],
                    ],
                    [
                        'status' => $studentData['status'],
                        'remarks' => $studentData['remarks'] ?? null,
                        'marked_by' => $facultyId,
                    ]
                );

                $createdRecords[] = $record;
            }

            return $createdRecords[0] ?? new Attendance();
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

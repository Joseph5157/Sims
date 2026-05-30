<?php

namespace App\Filament\Admin\Resources\AttendanceResource\Pages;

use App\Filament\Admin\Resources\AttendanceResource;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create — attendance is marked via the MarkAttendance faculty page
        ];
    }
}

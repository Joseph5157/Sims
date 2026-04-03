<?php

namespace App\Filament\Faculty\Resources\Attendances\Pages;

use App\Filament\Faculty\Resources\Attendances\AttendanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;
}

<?php

namespace App\Filament\Admin\Resources\AttendanceResource\Pages;

use App\Filament\Admin\Resources\AttendanceResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->url(AttendanceResource::getUrl('index'))
                ->color('gray'),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Attendance record updated';
    }
}

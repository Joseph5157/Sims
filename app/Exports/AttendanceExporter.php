<?php

namespace App\Exports;

use App\Models\Attendance;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Model;

class AttendanceExporter extends Exporter
{
    protected static ?string $model = Attendance::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('student.roll_number')
                ->label('Roll No')
                ->formatStateUsing(fn (?string $state, Model $record): string => $record->student->roll_number ?? ''),

            ExportColumn::make('student.user.name')
                ->label('Student Name')
                ->formatStateUsing(fn (?string $state, Model $record): string => $record->student->user->name ?? ''),

            ExportColumn::make('collegeClass.name')
                ->label('Class')
                ->formatStateUsing(fn (?string $state, Model $record): string => $record->collegeClass->name ?? ''),

            ExportColumn::make('attendance_date')
                ->label('Date')
                ->formatStateUsing(fn ($state): string => $state instanceof \DateTimeInterface ? $state->format('d M Y') : (string) $state),

            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn (?string $state): string => (string) $state),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return __('filament-actions::export.notifications.completed.body', [
            'count' => $export->total_rows,
        ]);
    }
}

<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AttendanceResource\Pages\EditAttendance;
use App\Filament\Admin\Resources\AttendanceResource\Pages\ListAttendances;
use App\Models\Attendance;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Attendance';

    protected static ?string $navigationLabel = 'Attendance Records';

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'excused' => 'Excused',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('notes')
                    ->label('Reason / Notes')
                    ->nullable()
                    ->rows(2),

                Forms\Components\Textarea::make('edit_reason')
                    ->label('Edit Reason')
                    ->helperText('Required: state why this attendance record is being modified.')
                    ->required()
                    ->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('collegeClass.name')
                    ->label('Class')
                    ->sortable(),

                Tables\Columns\TextColumn::make('attendance_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'excused' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('markedBy.name')
                    ->label('Marked By')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('edit_reason')
                    ->label('Edit Reason')
                    ->limit(40)
                    ->placeholder('—')
                    ->tooltip(fn (Attendance $record): ?string => $record->edit_reason),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['student.user', 'collegeClass', 'markedBy']))
            ->defaultSort('attendance_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('college_class_id')
                    ->label('Class')
                    ->relationship('collegeClass', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'excused' => 'Excused',
                    ]),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('to')->label('To'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('attendance_date', '>=', $data['from']))
                            ->when($data['to'], fn ($q) => $q->whereDate('attendance_date', '<=', $data['to']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'From: '.$data['from'];
                        }
                        if ($data['to'] ?? null) {
                            $indicators[] = 'To: '.$data['to'];
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendances::route('/'),
            'edit' => EditAttendance::route('/{record}/edit'),
        ];
    }
}

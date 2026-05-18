<?php

namespace App\Filament\Admin\Resources;

use App\Enums\DayOfWeek;
use App\Filament\Admin\Resources\TimetableSlotResource\Pages\CreateTimetableSlot;
use App\Filament\Admin\Resources\TimetableSlotResource\Pages\EditTimetableSlot;
use App\Filament\Admin\Resources\TimetableSlotResource\Pages\ListTimetableSlots;
use App\Models\AcademicYear;
use App\Models\TimetableSlot;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TimetableSlotResource extends Resource
{
    protected static ?string $model = TimetableSlot::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    protected static ?string $navigationLabel = 'Timetable';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'day_of_week';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        $dayOptions = collect(DayOfWeek::cases())
            ->mapWithKeys(fn (DayOfWeek $d): array => [$d->value => $d->label()])
            ->all();

        return $schema
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('college_class_id')
                        ->label('Class')
                        ->relationship('collegeClass', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(function (callable $set): void {
                            $set('subject_id', null);
                            $set('faculty_id', null);
                        }),

                    Forms\Components\Select::make('academic_year_id')
                        ->label('Academic Year')
                        ->options(fn (): array => AcademicYear::orderByDesc('start_year')->pluck('name', 'id')->all())
                        ->searchable()
                        ->nullable(),
                ]),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('subject_id')
                        ->label('Subject')
                        ->relationship('subject', 'name', fn ($query, callable $get) => $get('college_class_id')
                            ? $query->where('college_class_id', $get('college_class_id'))
                            : $query
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('faculty_id', null)),

                    Forms\Components\Select::make('faculty_id')
                        ->label('Faculty')
                        ->relationship('faculty', 'employee_id', fn ($query, callable $get) => $get('subject_id')
                            ? $query->whereHas('subjects', fn ($q) => $q->where('id', $get('subject_id')))
                            : $query
                        )
                        ->getOptionLabelFromRecordUsing(fn ($record): string => $record->user?->name ?? $record->employee_id)
                        ->required()
                        ->searchable()
                        ->preload(),
                ]),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('day_of_week')
                        ->label('Day')
                        ->options($dayOptions)
                        ->required(),

                    Forms\Components\TextInput::make('period_number')
                        ->label('Period No.')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(8),
                ]),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TimePicker::make('start_time')
                        ->label('Start Time')
                        ->seconds(false)
                        ->nullable(),

                    Forms\Components\TimePicker::make('end_time')
                        ->label('End Time')
                        ->seconds(false)
                        ->nullable(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $dayOptions = collect(DayOfWeek::cases())
            ->mapWithKeys(fn (DayOfWeek $d): array => [$d->value => $d->label()])
            ->all();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('collegeClass.name')
                    ->label('Class')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('faculty.user.name')
                    ->label('Faculty')
                    ->searchable(),

                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Day')
                    ->badge()
                    ->formatStateUsing(fn (?DayOfWeek $state): string => $state?->label() ?? '—')
                    ->color(fn (?DayOfWeek $state): string => $state?->color() ?? 'gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('period_number')
                    ->label('Period')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('End')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Academic Year')
                    ->placeholder('—'),
            ])
            ->defaultSort(fn ($query) => $query
                ->orderByRaw("CASE day_of_week WHEN 'monday' THEN 1 WHEN 'tuesday' THEN 2 WHEN 'wednesday' THEN 3 WHEN 'thursday' THEN 4 WHEN 'friday' THEN 5 WHEN 'saturday' THEN 6 ELSE 7 END")
                ->orderBy('period_number')
            )
            ->filters([
                Tables\Filters\SelectFilter::make('college_class_id')
                    ->label('Class')
                    ->relationship('collegeClass', 'name'),

                Tables\Filters\SelectFilter::make('day_of_week')
                    ->label('Day')
                    ->options($dayOptions),

                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Academic Year')
                    ->options(fn (): array => AcademicYear::orderByDesc('start_year')->pluck('name', 'id')->all()),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTimetableSlots::route('/'),
            'create' => CreateTimetableSlot::route('/create'),
            'edit' => EditTimetableSlot::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TimetableSlotResource\Pages\CreateTimetableSlot;
use App\Filament\Admin\Resources\TimetableSlotResource\Pages\EditTimetableSlot;
use App\Filament\Admin\Resources\TimetableSlotResource\Pages\ListTimetableSlots;
use App\Models\TimetableSlot;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TimetableSlotResource extends Resource
{
    protected static ?string $model = TimetableSlot::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'day';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('college_class_id')
                    ->relationship('collegeClass', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (callable $set) {
                        $set('subject_id', null);
                    }),

                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'name', function ($query, callable $get) {
                        $collegeClassId = $get('college_class_id');
                        if ($collegeClassId) {
                            return $query->where('college_class_id', $collegeClassId);
                        }
                        return $query;
                    })
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('faculty_id')
                    ->relationship('faculty', 'user.name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('day')
                    ->required()
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                    ]),

                Forms\Components\TextInput::make('period')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(10),

                Forms\Components\TextInput::make('room')
                    ->optional()
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
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
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('day')
                    ->sortable(),

                Tables\Columns\TextColumn::make('period')
                    ->sortable(),

                Tables\Columns\TextColumn::make('room'),
            ])
            ->defaultSort(fn ($query) => $query->orderBy('day')->orderBy('period'))
            ->filters([
                Tables\Filters\SelectFilter::make('college_class_id')
                    ->relationship('collegeClass', 'name'),
                Tables\Filters\SelectFilter::make('day')
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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

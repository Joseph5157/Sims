<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GradeResource\Pages\CreateGrade;
use App\Filament\Admin\Resources\GradeResource\Pages\EditGrade;
use App\Filament\Admin\Resources\GradeResource\Pages\ListGrades;
use App\Models\Grade;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'exam_type';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'user.name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('exam_type')
                    ->options([
                        'midterm' => 'Midterm',
                        'final' => 'Final',
                        'assignment' => 'Assignment',
                        'quiz' => 'Quiz',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('marks_obtained')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('total_marks')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('entered_by')
                    ->relationship('enteredBy', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload(),
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

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('exam_type')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('marks_obtained')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_marks')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subject_id')
                    ->relationship('subject', 'name'),
                Tables\Filters\SelectFilter::make('exam_type')
                    ->options([
                        'midterm' => 'Midterm',
                        'final' => 'Final',
                        'assignment' => 'Assignment',
                        'quiz' => 'Quiz',
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
            'index' => ListGrades::route('/'),
            'create' => CreateGrade::route('/create'),
            'edit' => EditGrade::route('/{record}/edit'),
        ];
    }
}

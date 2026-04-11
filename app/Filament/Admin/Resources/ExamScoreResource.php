<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ExamScoreResource\Pages\CreateExamScore;
use App\Filament\Admin\Resources\ExamScoreResource\Pages\EditExamScore;
use App\Filament\Admin\Resources\ExamScoreResource\Pages\ListExamScores;
use App\Models\Exam;
use App\Models\ExamScore;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ExamScoreResource extends Resource
{
    protected static ?string $model = ExamScore::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string|\UnitEnum|null $navigationGroup = 'Examinations';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('exam_id')
                    ->relationship('exam', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Exam $record): string => ($record->examGroup?->name ?? 'Exam').' - '.($record->subject?->name ?? 'Subject'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (callable $set): void {
                        $set('student_id', null);
                    }),

                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'roll_number', function ($query, Get $get) {
                        $examId = $get('exam_id');

                        if (! $examId) {
                            return $query->whereRaw('1 = 0');
                        }

                        $exam = Exam::with('examGroup')->find($examId);
                        $collegeClassId = $exam?->examGroup?->college_class_id;

                        if (! $collegeClassId) {
                            return $query->whereRaw('1 = 0');
                        }

                        return $query->where('college_class_id', $collegeClassId);
                    })
                    ->getOptionLabelFromRecordUsing(fn (Student $record): string => $record->user?->name ?? $record->roll_number)
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('marks_obtained')
                    ->numeric()
                    ->nullable(),

                Forms\Components\Toggle::make('absent')
                    ->default(false),

                Forms\Components\Textarea::make('remarks')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('exam')
                    ->label('Exam')
                    ->formatStateUsing(fn ($state, ExamScore $record): string => ($record->exam?->examGroup?->name ?? 'Exam').' - '.($record->exam?->subject?->name ?? 'Subject')),

                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('marks_obtained')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('absent')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'danger' : 'success')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Absent' : 'Present'),

                Tables\Columns\TextColumn::make('percentage')
                    ->label('Percentage')
                    ->badge()
                    ->color(fn ($state, ExamScore $record): string => $record->getPercentage() >= 75 ? 'success' : ($record->getPercentage() >= 50 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn ($state, ExamScore $record): string => number_format($record->getPercentage(), 2).'%'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('exam_id')
                    ->relationship('exam', 'id'),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExamScores::route('/'),
            'create' => CreateExamScore::route('/create'),
            'edit' => EditExamScore::route('/{record}/edit'),
        ];
    }
}

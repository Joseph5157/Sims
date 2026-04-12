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
use Illuminate\Validation\Rules\Unique;
use Illuminate\Database\Eloquent\Builder;

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
                    ->relationship('student', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => optional($record->user)->name ?? 'Student ' . $record->id)
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

                Forms\Components\TextInput::make('total_marks')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                Forms\Components\TextInput::make('marks_obtained')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->lte('total_marks')
                    ->helperText('Marks obtained cannot exceed total marks'),

                Forms\Components\Select::make('entered_by')
                    ->relationship('enteredBy', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Faculty members can only see grades for students in their assigned classes
        if ($user?->hasRole('faculty')) {
            $query->whereHas('student.collegeClass', function (Builder $q) use ($user) {
                $q->whereHas('faculty', function (Builder $fq) use ($user) {
                    $fq->where('user_id', $user->id);
                });
            });
        }

        return $query;
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

<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ExamResource\Pages\CreateExam;
use App\Filament\Admin\Resources\ExamResource\Pages\EditExam;
use App\Filament\Admin\Resources\ExamResource\Pages\ListExams;
use App\Models\Exam;
use App\Models\ExamGroup;
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

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Examinations';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('exam_group_id')
                    ->relationship('examGroup', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (callable $set): void {
                        $set('subject_id', null);
                    }),

                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'name', function ($query, Get $get) {
                        $examGroupId = $get('exam_group_id');

                        if (! $examGroupId) {
                            return $query->whereRaw('1 = 0');
                        }

                        $examGroup = ExamGroup::find($examGroupId);
                        $collegeClassId = $examGroup?->college_class_id;

                        if (! $collegeClassId) {
                            return $query->whereRaw('1 = 0');
                        }

                        return $query->where('college_class_id', $collegeClassId);
                    })
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\DatePicker::make('date')
                    ->nullable(),

                Forms\Components\TimePicker::make('start_time')
                    ->nullable(),

                Forms\Components\TimePicker::make('end_time')
                    ->nullable(),

                Forms\Components\TextInput::make('maximum_marks')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('minimum_marks')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('weightage')
                    ->numeric()
                    ->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('examGroup.name')
                    ->label('Exam Group')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('maximum_marks')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('minimum_marks')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('exam_group_id')
                    ->relationship('examGroup', 'name'),
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
            'index' => ListExams::route('/'),
            'create' => CreateExam::route('/create'),
            'edit' => EditExam::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ExamGroupResource\Pages\CreateExamGroup;
use App\Filament\Admin\Resources\ExamGroupResource\Pages\EditExamGroup;
use App\Filament\Admin\Resources\ExamGroupResource\Pages\ListExamGroups;
use App\Models\ExamGroup;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Route;

class ExamGroupResource extends Resource
{
    protected static ?string $model = ExamGroup::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Examinations';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('college_class_id')
                    ->relationship('collegeClass', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->label('Class'),

                Forms\Components\Select::make('exam_type')
                    ->options([
                        'marks' => 'Marks',
                        'grades' => 'Grades',
                    ])
                    ->required(),

                Forms\Components\DatePicker::make('start_date')
                    ->nullable(),

                Forms\Components\DatePicker::make('end_date')
                    ->nullable(),

                Forms\Components\Toggle::make('is_published')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('collegeClass.name')
                    ->label('Class')
                    ->sortable(),

                Tables\Columns\TextColumn::make('exam_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str($state)->headline()->toString())
                    ->color(fn (string $state): string => $state === 'marks' ? 'info' : 'warning')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('is_published')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Published' : 'Draft'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('manage_exams')
                    ->label('Manage Exams')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(function (ExamGroup $record): string {
                        $routeName = 'filament.admin.resources.exams.index';

                        if (! Route::has($routeName)) {
                            return '#';
                        }

                        return route($routeName, [
                            'tableFilters' => [
                                'exam_group_id' => [
                                    'value' => $record->getKey(),
                                ],
                            ],
                        ]);
                    })
                    ->visible(fn (): bool => Route::has('filament.admin.resources.exams.index')),
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
            'index' => ListExamGroups::route('/'),
            'create' => CreateExamGroup::route('/create'),
            'edit' => EditExamGroup::route('/{record}/edit'),
        ];
    }
}

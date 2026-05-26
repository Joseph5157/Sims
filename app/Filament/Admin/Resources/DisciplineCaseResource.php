<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DisciplineCaseResource\Pages\CreateDisciplineCase;
use App\Filament\Admin\Resources\DisciplineCaseResource\Pages\EditDisciplineCase;
use App\Filament\Admin\Resources\DisciplineCaseResource\Pages\ListDisciplineCases;
use App\Models\DisciplineCase;
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

class DisciplineCaseResource extends Resource
{
    protected static ?string $model = DisciplineCase::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'roll_number')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? $record->roll_number)
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('faculty_id')
                    ->relationship('faculty', 'employee_id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? $record->employee_id)
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->rows(3),

                Forms\Components\Select::make('severity')
                    ->required()
                    ->options([
                        'Low' => 'Low',
                        'Medium' => 'Medium',
                        'High' => 'High',
                        'Critical' => 'Critical',
                    ]),

                Forms\Components\FileUpload::make('attachment')
                    ->optional(),

                Forms\Components\Select::make('status')
                    ->default('Pending')
                    ->options([
                        'Pending' => 'Pending',
                        'Under Review' => 'Under Review',
                        'Resolved' => 'Resolved',
                    ]),

                Forms\Components\Hidden::make('admin_id'),

                Forms\Components\DatePicker::make('resolved_at')
                    ->optional()
                    ->visible(fn (callable $get) => $get('status') === 'Resolved'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Student')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('faculty.user.name')
                    ->label('Faculty')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Low' => 'success',
                        'Medium' => 'warning',
                        'High' => 'danger',
                        'Critical' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => $state),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state),

                Tables\Columns\TextColumn::make('resolved_at')
                    ->date()
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['student.user', 'faculty.user']))
            ->filters([
                Tables\Filters\SelectFilter::make('severity')
                    ->options([
                        'Low' => 'Low',
                        'Medium' => 'Medium',
                        'High' => 'High',
                        'Critical' => 'Critical',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Under Review' => 'Under Review',
                        'Resolved' => 'Resolved',
                    ]),
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
            'index' => ListDisciplineCases::route('/'),
            'create' => CreateDisciplineCase::route('/create'),
            'edit' => EditDisciplineCase::route('/{record}/edit'),
        ];
    }
}

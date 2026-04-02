<?php

namespace App\Filament\Resources\DisciplineCases;

use App\Filament\Resources\Concerns\InteractsWithRoleAccess;
use App\Filament\Resources\DisciplineCases\Pages\CreateDisciplineCase;
use App\Filament\Resources\DisciplineCases\Pages\EditDisciplineCase;
use App\Filament\Resources\DisciplineCases\Pages\ListDisciplineCases;
use App\Models\DisciplineCase;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class DisciplineCaseResource extends Resource
{
    use InteractsWithRoleAccess;

    protected static ?string $model = DisciplineCase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function canViewAny(): bool
    {
        return static::userHasAnyRole(['admin', 'faculty']);
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return static::userHasAnyRole(['admin', 'faculty']);
    }

    public static function canEdit(Model $record): bool
    {
        return static::userHasAnyRole(['admin', 'faculty']);
    }

    public static function canDelete(Model $record): bool
    {
        return static::userHasRole('admin');
    }

    public static function canDeleteAny(): bool
    {
        return static::userHasRole('admin');
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('student_id')
                    ->relationship('student', 'roll_number')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Student'),

                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),

                Select::make('severity')
                    ->options([
                        'Low' => 'Low',
                        'Medium' => 'Medium',
                        'High' => 'High',
                        'Critical' => 'Critical',
                    ])
                    ->required(),

                Select::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Under Review' => 'Under Review',
                        'Resolved' => 'Resolved',
                    ])
                    ->required(),

                Select::make('faculty_id')
                    ->relationship('faculty', 'employee_id')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Reported By'),

                TextInput::make('attachment')
                    ->maxLength(255),

                DateTimePicker::make('resolved_at')
                    ->label('Resolved At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.user.name')
                    ->label('Student')
                    ->searchable(),

                TextColumn::make('student.roll_number')
                    ->label('Roll Number')
                    ->searchable(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->limit(50),

                TextColumn::make('severity')
                    ->sortable(),

                TextColumn::make('status')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->date()
                    ->label('Date'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
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
            'index' => ListDisciplineCases::route('/'),
            'create' => CreateDisciplineCase::route('/create'),
            'edit' => EditDisciplineCase::route('/{record}/edit'),
        ];
    }
}

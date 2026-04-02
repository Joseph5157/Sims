<?php

namespace App\Filament\Resources\CollegeClasses;

use App\Filament\Resources\Concerns\InteractsWithRoleAccess;
use App\Filament\Resources\CollegeClasses\Pages\CreateCollegeClass;
use App\Filament\Resources\CollegeClasses\Pages\EditCollegeClass;
use App\Filament\Resources\CollegeClasses\Pages\ListCollegeClasses;
use App\Filament\Resources\CollegeClasses\Schemas\CollegeClassForm;
use App\Filament\Resources\CollegeClasses\Tables\CollegeClassesTable;
use App\Models\CollegeClass;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CollegeClassResource extends Resource
{
    use InteractsWithRoleAccess;

    protected static ?string $model = CollegeClass::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

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
        return static::userHasRole('admin');
    }

    public static function canEdit(Model $record): bool
    {
        return static::userHasRole('admin');
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
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('section')
                    ->maxLength(50),

                Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('semester')
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                        6 => '6',
                        7 => '7',
                        8 => '8',
                    ])
                    ->required(),

                TextInput::make('academic_year')
                    ->required()
                    ->numeric()
                    ->maxLength(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Class Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('section'),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('semester')
                    ->label('Semester')
                    ->sortable(),
                TextColumn::make('academic_year')
                    ->label('Academic Year')
                    ->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
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
            'index' => ListCollegeClasses::route('/'),
            'create' => CreateCollegeClass::route('/create'),
            'edit' => EditCollegeClass::route('/{record}/edit'),
        ];
    }
}

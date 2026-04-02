<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Concerns\InteractsWithRoleAccess;
use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Models\Student;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class StudentResource extends Resource
{
    use InteractsWithRoleAccess;

    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'roll_number';

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
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Student Name'),

                TextInput::make('roll_number')
                    ->required()
                    ->unique('students', 'roll_number', ignoreRecord: true)
                    ->label('Roll Number'),

                Select::make('department_id')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Department'),

                Select::make('college_class_id')
                    ->relationship('collegeClass', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('College Class'),

                TextInput::make('admission_year')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roll_number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('department.name')
                    ->label('Department'),

                TextColumn::make('collegeClass.name')
                    ->label('College Class'),

                TextColumn::make('created_at')
                    ->label('Added On')
                    ->dateTime('d M Y'),
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
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }
}

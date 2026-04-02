<?php

namespace App\Filament\Resources\Faculties;

use App\Filament\Resources\Concerns\InteractsWithRoleAccess;
use App\Filament\Resources\Faculties\Pages\CreateFaculty;
use App\Filament\Resources\Faculties\Pages\EditFaculty;
use App\Filament\Resources\Faculties\Pages\ListFaculties;
use App\Models\Faculty;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FacultyResource extends Resource
{
    use InteractsWithRoleAccess;

    protected static ?string $model = Faculty::class;

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
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('User Account'),

                Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Department'),

                TextInput::make('employee_id')
                    ->required()
                    ->unique('faculties', 'employee_id', ignoreRecord: true)
                    ->maxLength(50)
                    ->label('Employee ID'),

                TextInput::make('qualification')
                    ->maxLength(255),

                DatePicker::make('joining_date')
                    ->label('Joining Date'),

                TextInput::make('phone')
                    ->tel()
                    ->maxLength(20),

                Textarea::make('specialization')
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Faculty Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('employee_id')
                    ->label('Employee ID'),

                TextColumn::make('qualification'),

                TextColumn::make('joining_date')
                    ->date()
                    ->label('Joining Date'),

                TextColumn::make('phone'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => ListFaculties::route('/'),
            'create' => CreateFaculty::route('/create'),
            'edit' => EditFaculty::route('/{record}/edit'),
        ];
    }
}

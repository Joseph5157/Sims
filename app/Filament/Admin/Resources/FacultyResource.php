<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FacultyResource\Pages\CreateFaculty;
use App\Filament\Admin\Resources\FacultyResource\Pages\EditFaculty;
use App\Filament\Admin\Resources\FacultyResource\Pages\ListFaculties;
use App\Models\Faculty;
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

class FacultyResource extends Resource
{
    protected static ?string $model = Faculty::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'People';

    protected static ?string $navigationLabel = 'Faculty';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'employee_id';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Personal Info')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Full Name')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                        ]),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('phone')
                                ->tel()
                                ->nullable()
                                ->maxLength(20),

                            Forms\Components\DatePicker::make('joining_date')
                                ->label('Joining Date')
                                ->nullable(),
                        ]),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('qualification')
                                ->label('Qualification')
                                ->nullable()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('designation')
                                ->label('Designation')
                                ->nullable()
                                ->maxLength(255),
                        ]),
                    ]),

                Forms\Components\Section::make('Department')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('department_id')
                                ->label('Department')
                                ->relationship('department', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),

                            Forms\Components\TextInput::make('employee_id')
                                ->label('Employee Code')
                                ->required()
                                ->unique('faculties', 'employee_id', ignoreRecord: true)
                                ->maxLength(50),
                        ]),
                    ]),

                Forms\Components\Section::make('Account')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->helperText(fn (string $operation): string => $operation === 'edit'
                                ? 'Leave blank to keep the current password.'
                                : '')
                            ->minLength(8)
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee_id')
                    ->label('Employee Code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('joining_date')
                    ->label('Joined')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subjects_count')
                    ->label('Subjects')
                    ->counts('subjects')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('employee_id')
            ->filters([
                Tables\Filters\SelectFilter::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name'),
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
        return [];
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

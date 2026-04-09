<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StudentResource\Pages\CreateStudent;
use App\Filament\Admin\Resources\StudentResource\Pages\EditStudent;
use App\Filament\Admin\Resources\StudentResource\Pages\ListStudents;
use App\Models\Student;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\CollegeClass;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'roll_number';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (callable $set) {
                        $set('college_class_id', null);
                    }),

                Forms\Components\Select::make('college_class_id')
                    ->relationship('collegeClass', 'name', function ($query, callable $get) {
                        $departmentId = $get('department_id');
                        if ($departmentId) {
                            return $query->where('department_id', $departmentId);
                        }
                        return $query;
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Class'),

                Forms\Components\TextInput::make('roll_number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),

                Forms\Components\DatePicker::make('date_of_birth')
                    ->optional(),

                Forms\Components\TextInput::make('phone')
                    ->optional()
                    ->maxLength(15),

                Forms\Components\Textarea::make('address')
                    ->optional()
                    ->rows(3),

                Forms\Components\TextInput::make('admission_year')
                    ->numeric()
                    ->optional(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('roll_number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable(),

                Tables\Columns\TextColumn::make('collegeClass.name')
                    ->label('Class')
                    ->sortable(),

                Tables\Columns\TextColumn::make('admission_year')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department_id')
                    ->relationship('department', 'name'),
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
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }
}

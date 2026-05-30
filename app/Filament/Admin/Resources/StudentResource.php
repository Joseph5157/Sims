<?php

namespace App\Filament\Admin\Resources;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Filament\Admin\Resources\StudentResource\Pages\CreateStudent;
use App\Filament\Admin\Resources\StudentResource\Pages\EditStudent;
use App\Filament\Admin\Resources\StudentResource\Pages\ListStudents;
use App\Models\AcademicYear;
use App\Models\Student;
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
use Illuminate\Database\Eloquent\Builder;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'People';

    protected static ?string $navigationLabel = 'Students';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'roll_number';

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
                                ->unique('users', 'email', ignoreRecord: false)
                                ->maxLength(255),
                        ]),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('date_of_birth')
                                ->label('Date of Birth')
                                ->nullable(),

                            Forms\Components\Select::make('gender')
                                ->label('Gender')
                                ->options(collect(Gender::cases())->mapWithKeys(
                                    fn (Gender $g): array => [$g->value => $g->label()]
                                ))
                                ->nullable(),
                        ]),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('blood_group')
                                ->label('Blood Group')
                                ->maxLength(5)
                                ->nullable(),

                            Forms\Components\TextInput::make('phone')
                                ->tel()
                                ->nullable()
                                ->maxLength(20),
                        ]),

                        Forms\Components\Textarea::make('address')
                            ->nullable()
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Academic Info')
                    ->icon('heroicon-o-book-open')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('roll_number')
                                ->label('Roll Number')
                                ->required()
                                ->unique('students', 'roll_number', ignoreRecord: true)
                                ->maxLength(50),

                            Forms\Components\TextInput::make('admission_number')
                                ->label('Admission Number')
                                ->unique('students', 'admission_number', ignoreRecord: true)
                                ->nullable()
                                ->maxLength(50),
                        ]),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('college_class_id')
                                ->label('Class')
                                ->relationship('collegeClass', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('department_id')
                                ->label('Department')
                                ->relationship('department', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),
                        ]),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('academic_year_id')
                                ->label('Academic Year')
                                ->options(fn (): array => AcademicYear::orderByDesc('start_year')->pluck('name', 'id')->all())
                                ->searchable()
                                ->nullable(),

                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options(collect(StudentStatus::cases())->mapWithKeys(
                                    fn (StudentStatus $s): array => [$s->value => $s->label()]
                                ))
                                ->default(StudentStatus::Active->value)
                                ->required(),
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Faculty members can only see students from their assigned classes
        if ($user?->hasRole('faculty')) {
            $query->whereHas('collegeClass', function (Builder $q) use ($user) {
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('roll_number')
                    ->label('Roll No.')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('admission_number')
                    ->label('Admission No.')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('collegeClass.name')
                    ->label('Class')
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable(),

                Tables\Columns\TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->formatStateUsing(fn (?Gender $state): string => $state?->label() ?? '—')
                    ->color(fn (?Gender $state): string => match ($state) {
                        Gender::Male => 'info',
                        Gender::Female => 'primary',
                        Gender::Other => 'gray',
                        null => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (StudentStatus $state): string => $state->label())
                    ->color(fn (StudentStatus $state): string => $state->color()),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['user', 'collegeClass', 'department']))
            ->defaultSort('roll_number')
            ->filters([
                Tables\Filters\SelectFilter::make('college_class_id')
                    ->label('Class')
                    ->relationship('collegeClass', 'name'),

                Tables\Filters\SelectFilter::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(StudentStatus::cases())->mapWithKeys(
                        fn (StudentStatus $s): array => [$s->value => $s->label()]
                    )),

                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Academic Year')
                    ->options(fn (): array => AcademicYear::orderByDesc('start_year')->pluck('name', 'id')->all()),
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
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Faculty\Resources\Attendances;

use App\Models\Attendance;
use App\Models\CollegeClass;
use App\Models\Subject;
use App\Models\User;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationLabel = 'Mark Attendance';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User ? $user->hasRole('faculty') : false;
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Select::make('college_class_id')
                    ->label('Select Class')
                    ->options(CollegeClass::pluck('name', 'id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('subject_id', null)),

                Forms\Components\Select::make('subject_id')
                    ->label('Select Subject')
                    ->options(function (Get $get) {
                        $classId = $get('college_class_id');

                        return $classId
                            ? Subject::where('college_class_id', $classId)->pluck('name', 'id')
                            : [];
                    })
                    ->required()
                    ->live(),

                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->default(now()),

                \Filament\Schemas\Components\Section::make('Students Attendance')
                    ->schema([
                        Forms\Components\Repeater::make('students')
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('student_id')
                                    ->label('Student')
                                    ->options(function () {
                                        return \App\Models\Student::with('user')
                                            ->get()
                                            ->pluck('user.name', 'id')
                                            ->mapWithKeys(fn ($name, $id) => [$id => $name]);
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'present' => '✅ Present',
                                        'absent'  => '❌ Absent',
                                        'late'    => '⚠️ Late',
                                    ])
                                    ->default('present')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Student')
                            ->reorderable(false)
                            ->cloneable(false),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.roll_number')
                    ->label('Roll No')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Student Name'),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject'),

                Tables\Columns\TextColumn::make('date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'present',
                        'danger'  => 'absent',
                        'warning' => 'late',
                    ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Faculty\Resources\Attendances\Pages\ListAttendances::route('/'),
        ];
    }
}

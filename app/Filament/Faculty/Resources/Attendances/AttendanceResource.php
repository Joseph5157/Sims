<?php

namespace App\Filament\Faculty\Resources\Attendances;

use App\Models\Attendance;
use App\Models\CollegeClass;
use App\Models\Student;
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
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $classId = $get('college_class_id');

                        $set('subject_id', null);

                        if ($classId) {
                            $students = Student::where('college_class_id', $classId)
                                ->with('user')
                                ->get()
                                ->map(function ($student) {
                                    return [
                                        'student_id' => $student->id,
                                        'status' => 'present',
                                    ];
                                })
                                ->toArray();

                            $set('students', $students);
                        } else {
                            $set('students', []);
                        }
                    }),

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

                \Filament\Schemas\Components\Section::make('Mark Attendance for Students')
                    ->schema([
                        Forms\Components\Repeater::make('students')
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('student_id')
                                    ->label('Student')
                                    ->options(function (Get $get) {
                                        $classId = $get('../../college_class_id');

                                        return $classId
                                            ? Student::where('college_class_id', $classId)
                                                ->with('user')
                                                ->get()
                                                ->pluck('user.name', 'id')
                                            : [];
                                    })
                                    ->required()
                                    ->searchable()
                                    ->disabled()
                                    ->dehydrated(),

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
                            ->addActionLabel('Add Extra Student')
                            ->reorderable(false)
                            ->cloneable(false)
                            ->defaultItems(0),
                    ]),
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

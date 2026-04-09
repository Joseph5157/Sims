<?php

namespace App\Filament\Faculty\Resources;

use App\Exports\AttendanceExporter;
use App\Models\Attendance;
use App\Models\CollegeClass;
use App\Models\Student;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions as SchemaActions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AttendanceResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Academic';

    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationLabel = 'Mark Attendance';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->hasRole('faculty') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('college_class_id')
                    ->label('Select Class')
                    ->options(CollegeClass::pluck('name', 'id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set): void {
                        $classId = $get('college_class_id');

                        if (! $classId) {
                            $set('students', []);

                            return;
                        }

                        $students = Student::where('college_class_id', $classId)
                            ->with('user')
                            ->get()
                            ->map(function (Student $student): array {
                                return [
                                    'student_id' => $student->id,
                                    'status' => 'present',
                                    'notes' => null,
                                ];
                            })
                            ->toArray();

                        $set('students', $students);
                    }),

                DatePicker::make('attendance_date')
                    ->required()
                    ->default(now())
                    ->maxDate(now()),

                Section::make('Mark Attendance for Students')
                    ->description('All students from the selected class are auto-loaded below. Change status as needed.')
                    ->schema([
                        Repeater::make('students')
                            ->label('')
                            ->schema([
                                Select::make('student_id')
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
                                    ->disabled()
                                    ->dehydrated(true),

                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'present' => 'Present',
                                        'absent' => 'Absent',
                                        'late' => 'Late',
                                        'excused' => 'Excused',
                                    ])
                                    ->default('present')
                                    ->required(),

                                Textarea::make('notes')
                                    ->label('Notes')
                                    ->rows(1)
                                    ->placeholder('Optional notes...'),
                            ])
                            ->columns(3)
                            ->addActionLabel('Add Extra Student')
                            ->reorderable(false)
                            ->cloneable(false)
                            ->defaultItems(0)
                            ->minItems(1),
                    ]),

                SchemaActions::make([
                    Action::make('mark_all_present')
                        ->label('Mark All Present')
                        ->color('success')
                        ->action(function ($livewire): void {
                            $students = $livewire->data['students'] ?? [];

                            foreach ($students as &$student) {
                                $student['status'] = 'present';
                            }

                            $livewire->data['students'] = $students;
                        }),

                    Action::make('mark_all_absent')
                        ->label('Mark All Absent')
                        ->color('danger')
                        ->action(function ($livewire): void {
                            $students = $livewire->data['students'] ?? [];

                            foreach ($students as &$student) {
                                $student['status'] = 'absent';
                            }

                            $livewire->data['students'] = $students;
                        }),

                    Action::make('mark_all_late')
                        ->label('Mark All Late')
                        ->color('warning')
                        ->action(function ($livewire): void {
                            $students = $livewire->data['students'] ?? [];

                            foreach ($students as &$student) {
                                $student['status'] = 'late';
                            }

                            $livewire->data['students'] = $students;
                        }),
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
                    ->label('Student Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('collegeClass.name')
                    ->label('Class')
                    ->searchable(),

                Tables\Columns\TextColumn::make('attendance_date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'present',
                        'danger' => 'absent',
                        'warning' => 'late',
                        'info' => 'excused',
                    ]),
            ])
            ->defaultSort('attendance_date', 'desc')
            ->headerActions([
                ExportAction::make()
                    ->label('Export Today\'s Attendance')
                    ->exporter(AttendanceExporter::class),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Faculty\Resources\Attendances\Pages\ListAttendances::route('/'),
            'create' => \App\Filament\Faculty\Resources\Attendances\Pages\CreateAttendance::route('/create'),
            'edit' => \App\Filament\Faculty\Resources\Attendances\Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}

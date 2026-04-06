<?php

namespace App\Filament\Faculty\Pages;

use App\Models\Attendance;
use App\Models\CollegeClass;
use App\Models\Student;
use App\Models\Subject;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class MonthlyAttendance extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Monthly Attendance';
    protected static string|UnitEnum|null $navigationGroup = 'Academic';
    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.faculty.pages.monthly-attendance';

    public ?int $college_class_id = null;
    public ?int $subject_id = null;
    public string $month = '';

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('college_class_id')
                    ->label('Select Class')
                    ->options(CollegeClass::pluck('name', 'id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set): mixed => $set('subject_id', null)),

                Select::make('subject_id')
                    ->label('Select Subject')
                    ->options(fn (Get $get) =>
                        $get('college_class_id')
                            ? Subject::where('college_class_id', $get('college_class_id'))->pluck('name', 'id')
                            : []
                    )
                    ->required()
                    ->live(),

                DatePicker::make('month')
                    ->label('Month')
                    ->format('Y-m')
                    ->displayFormat('F Y')           // ← This makes it "April 2026"
                    ->default(now())
                    ->live()
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->college_class_id
                ? Student::where('college_class_id', $this->college_class_id)
                : Student::query()->where('id', 0)
            )
            ->columns([
                TextColumn::make('roll_number')->label('Roll No')->sortable(),
                TextColumn::make('user.name')->label('Student Name')->searchable(),
            ])
            ->headerActions([
                Action::make('mark_entire_month')
                    ->label('Mark All Students for Entire Month')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Select::make('status')
                            ->label('Status for every day of the month')
                            ->options([
                                'present' => '✅ Present',
                                'absent'  => '❌ Absent',
                                'late'    => '⚠️ Late',
                            ])
                            ->required()
                            ->default('present'),
                    ])
                    ->action(function (array $data) {
                        $this->markAttendanceForEntireMonth($data['status']);
                    }),
            ]);
    }

    private function markAttendanceForEntireMonth(string $status): void
    {
        if (!$this->college_class_id || !$this->subject_id) return;

        $start = Carbon::parse($this->month)->startOfMonth();
        $days = $start->daysInMonth;
        $students = Student::where('college_class_id', $this->college_class_id)->get();

        foreach ($students as $student) {
            for ($i = 0; $i < $days; $i++) {
                $date = $start->copy()->addDays($i);
                Attendance::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $this->subject_id,
                        'date'       => $date,
                    ],
                    [
                        'status'    => $status,
                        'marked_by' => Auth::id(),
                        'remarks'   => null,
                    ]
                );
            }
        }

        Notification::make()
            ->title('✅ Success')
            ->body('Attendance for the entire month has been saved for all students.')
            ->success()
            ->send();
    }
}

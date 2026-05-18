<?php

namespace App\Filament\Faculty\Pages;

use App\Models\Attendance;
use App\Models\CollegeClass;
use App\Models\Faculty;
use App\Models\TimetableSlot;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class AttendanceHistory extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected string $view = 'filament.faculty.pages.attendance-history';

    protected static string|UnitEnum|null $navigationGroup = 'Academic';

    protected static ?string $navigationLabel = 'Attendance History';

    protected static ?int $navigationSort = 3;

    public ?int $filterClassId = null;

    /** @var array<int, string> [id => name] */
    public array $facultyClasses = [];

    /** @var array<int, array{date: string, class_name: string, class_id: int, present: int, absent: int, late: int, total: int}> */
    public array $history = [];

    public function mount(): void
    {
        $this->loadFacultyClasses();
        $this->loadHistory();
    }

    private function loadFacultyClasses(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $faculty = Faculty::where('user_id', $user->id)->first();

        if (! $faculty) {
            $this->facultyClasses = [];

            return;
        }

        $classIds = TimetableSlot::where('faculty_id', $faculty->id)
            ->pluck('college_class_id')
            ->unique()
            ->values();

        $this->facultyClasses = CollegeClass::whereIn('id', $classIds)
            ->pluck('name', 'id')
            ->toArray();

        if (empty($this->facultyClasses)) {
            $this->facultyClasses = CollegeClass::where('department_id', $faculty->department_id)
                ->pluck('name', 'id')
                ->toArray();
        }
    }

    public function updatedFilterClassId(): void
    {
        $this->loadHistory();
    }

    public function loadHistory(): void
    {
        $classIds = $this->filterClassId
            ? [$this->filterClassId]
            : array_keys($this->facultyClasses);

        if (empty($classIds)) {
            $this->history = [];

            return;
        }

        $since = now()->subDays(30)->toDateString();

        $records = Attendance::whereIn('college_class_id', $classIds)
            ->where('attendance_date', '>=', $since)
            ->with('collegeClass')
            ->orderBy('attendance_date', 'desc')
            ->get();

        // Group by date + class
        $grouped = [];
        foreach ($records as $rec) {
            $key = $rec->attendance_date->toDateString().'|'.$rec->college_class_id;

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'date' => $rec->attendance_date->format('d M Y'),
                    'date_raw' => $rec->attendance_date->toDateString(),
                    'class_name' => $rec->collegeClass?->name ?? '—',
                    'class_id' => $rec->college_class_id,
                    'present' => 0,
                    'absent' => 0,
                    'late' => 0,
                    'total' => 0,
                ];
            }

            match ($rec->status) {
                'present' => $grouped[$key]['present']++,
                'absent' => $grouped[$key]['absent']++,
                'late' => $grouped[$key]['late']++,
                default => null,
            };

            $grouped[$key]['total']++;
        }

        $this->history = array_values($grouped);
    }
}

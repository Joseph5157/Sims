<?php

namespace App\Filament\Faculty\Widgets;

use App\Filament\Faculty\Pages\MarkAttendance;
use App\Models\Attendance;
use App\Models\CollegeClass;
use App\Models\Faculty;
use App\Models\TimetableSlot;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class TodayAttendanceWidget extends Widget
{
    protected string $view = 'filament.faculty.widgets.today-attendance';

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    public function getTodayClasses(): array
    {
        /** @var User $user */
        $user = Auth::user();
        $faculty = Faculty::where('user_id', $user->id)->first();

        if (! $faculty) {
            return [];
        }

        $today = strtolower(now()->format('l')); // 'monday', 'tuesday', etc.

        $classIds = TimetableSlot::where('faculty_id', $faculty->id)
            ->where('day', $today)
            ->pluck('college_class_id')
            ->unique()
            ->values();

        if ($classIds->isEmpty()) {
            return [];
        }

        $classes = CollegeClass::whereIn('id', $classIds)->get()->keyBy('id');
        $todayDate = now()->toDateString();

        $markedClassIds = Attendance::where('attendance_date', $todayDate)
            ->whereIn('college_class_id', $classIds)
            ->pluck('college_class_id')
            ->unique()
            ->values()
            ->toArray();

        $result = [];
        foreach ($classIds as $classId) {
            $class = $classes[$classId] ?? null;
            $result[] = [
                'id' => $classId,
                'name' => $class?->name ?? '—',
                'marked' => in_array($classId, $markedClassIds, true),
                'url' => MarkAttendance::getUrl(),
            ];
        }

        return $result;
    }
}

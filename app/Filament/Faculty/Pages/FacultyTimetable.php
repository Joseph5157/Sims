<?php

namespace App\Filament\Faculty\Pages;

use App\Models\Faculty;
use App\Models\TimetableSlot;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use BackedEnum;
use Illuminate\Support\Facades\Auth;

class FacultyTimetable extends Page
{
    protected string $view = 'filament.faculty.pages.faculty-timetable';

    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    public Collection $timetableSlots;

    public function mount(): void
    {
        $faculty = Faculty::where('user_id', Auth::id())->first();
        if ($faculty) {
            $this->timetableSlots = TimetableSlot::with(['subject', 'collegeClass'])
                ->where('faculty_id', $faculty->id)
                ->get()
                ->groupBy('day')
                ->sortKeysUsing(function ($a, $b) {
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    return array_search($a, $days) <=> array_search($b, $days);
                });
        } else {
            $this->timetableSlots = collect();
        }
    }
}

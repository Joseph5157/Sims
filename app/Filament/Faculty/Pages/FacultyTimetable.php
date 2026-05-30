<?php

namespace App\Filament\Faculty\Pages;

use App\Enums\DayOfWeek;
use App\Models\Faculty;
use App\Models\TimetableSlot;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class FacultyTimetable extends Page
{
    protected string $view = 'filament.faculty.pages.faculty-timetable';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'My Timetable';

    protected static ?string $slug = 'timetable';

    protected static ?int $navigationSort = 2;

    /**
     * Ordered list of days with their slots.
     * Shape: [ ['day' => DayOfWeek|string, 'label' => 'Monday', 'slots' => Collection] ]
     *
     * @var array<int, array{label: string, value: string, slots: Collection}>
     */
    public array $timetable = [];

    /** Today's day-of-week value string for highlighting, e.g. 'monday' */
    public string $todayValue = '';

    public function mount(): void
    {
        $this->todayValue = strtolower(now()->format('l'));

        $faculty = Faculty::where('user_id', Auth::id())->first();

        if (! $faculty) {
            $this->timetable = [];

            return;
        }

        $slots = TimetableSlot::with(['subject', 'collegeClass'])
            ->where('faculty_id', $faculty->id)
            ->get();

        // Group by day_of_week enum if available, else fall back to legacy 'day' string
        $grouped = [];

        foreach ($slots as $slot) {
            /** @var TimetableSlot $slot */
            $dayEnum = $slot->day_of_week;

            if ($dayEnum instanceof DayOfWeek) {
                $key = $dayEnum->value;
                $label = $dayEnum->label();
                $order = $dayEnum->sortOrder();
            } else {
                // legacy day string column
                $legacyDay = $slot->day ?? 'Unknown';
                $key = strtolower($legacyDay);
                $label = ucfirst($legacyDay);
                $dayOrder = ['monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6];
                $order = $dayOrder[$key] ?? 99;
            }

            if (! isset($grouped[$key])) {
                $grouped[$key] = ['label' => $label, 'value' => $key, 'order' => $order, 'slots' => collect()];
            }

            $grouped[$key]['slots']->push($slot);
        }

        // Sort days and slots within each day
        usort($grouped, fn (array $a, array $b): int => $a['order'] <=> $b['order']);

        foreach ($grouped as &$day) {
            $day['slots'] = $day['slots']->sortBy(fn (TimetableSlot $s): int => $s->period_number ?? (int) ($s->period ?? 0));
        }
        unset($day);

        $this->timetable = $grouped;
    }
}

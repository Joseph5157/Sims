<?php

namespace App\Filament\Faculty\Widgets;

use App\Models\Faculty;
use App\Models\TimetableSlot;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class TodayScheduleWidget extends Widget
{
    protected string $view = 'filament.faculty.widgets.today-schedule';

    protected static ?int $sort = 3;

    public function getViewData(): array
    {
        try {
            $user = Auth::user();
            $faculty = Faculty::where('user_id', $user?->id)->first();

            if (! $faculty) {
                return [
                    'slots' => collect(),
                    'today' => now()->format('l'),
                ];
            }

            $slots = TimetableSlot::where('faculty_id', $faculty->id)
                ->where('day', now()->format('l'))
                ->with(['subject', 'collegeClass'])
                ->orderBy('period')
                ->get();

            return [
                'slots' => $slots,
                'today' => now()->format('l'),
            ];
        } catch (\Throwable $e) {
            return [
                'slots' => collect(),
                'today' => now()->format('l'),
            ];
        }
    }
}

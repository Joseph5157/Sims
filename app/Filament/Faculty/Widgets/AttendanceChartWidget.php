<?php

namespace App\Filament\Faculty\Widgets;

use App\Models\Attendance;
use App\Models\TimetableSlot;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected string $color = 'success';

    protected ?string $heading = 'My Classes Attendance This Month';

    protected function getData(): array
    {
        $facultyId = Auth::user()?->facultyProfile?->id;

        if (! $facultyId) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $classIds = TimetableSlot::query()
            ->where('faculty_id', $facultyId)
            ->distinct()
            ->pluck('college_class_id');

        if ($classIds->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        [$start, $end] = $this->getMonthRange();

        $rows = Attendance::query()
            ->join('college_classes', 'attendances.college_class_id', '=', 'college_classes.id')
            ->whereIn('attendances.college_class_id', $classIds)
            ->whereBetween('attendances.attendance_date', [$start, $end])
            ->groupBy('attendances.college_class_id', 'college_classes.name')
            ->orderBy('college_classes.name')
            ->selectRaw('college_classes.name as class_name')
            ->selectRaw("SUM(CASE WHEN attendances.status IN ('present', 'late', 'excused') THEN 1 ELSE 0 END) as present_count")
            ->selectRaw("SUM(CASE WHEN attendances.status = 'absent' THEN 1 ELSE 0 END) as absent_count")
            ->get();

        $labels = $rows->pluck('class_name')->all();
        $data = $rows
            ->map(function ($row): float {
                $present = (int) $row->present_count;
                $absent = (int) $row->absent_count;
                $total = $present + $absent;

                return $total > 0 ? round(($present / $total) * 100, 1) : 0.0;
            })
            ->all();

        $palette = [
            'rgba(59, 130, 246, 0.75)', // blue
            'rgba(20, 184, 166, 0.75)', // teal
            'rgba(245, 158, 11, 0.75)', // amber
            'rgba(168, 85, 247, 0.75)', // purple
            'rgba(249, 115, 22, 0.75)', // orange
            'rgba(239, 68, 68, 0.75)', // red
            'rgba(34, 197, 94, 0.75)', // green
        ];

        $colors = array_map(
            fn (int $index): string => $palette[$index % count($palette)],
            array_keys($data),
        );

        return [
            'datasets' => [
                [
                    'label' => 'Attendance %',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => 'rgba(255, 255, 255, 0.25)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function getMonthRange(): array
    {
        $start = Carbon::now()->startOfMonth()->toDateString();
        $end = Carbon::now()->endOfMonth()->toDateString();

        return [$start, $end];
    }
}

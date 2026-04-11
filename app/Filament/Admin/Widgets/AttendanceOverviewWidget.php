<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AttendanceOverviewWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected string $color = 'success';

    protected ?string $heading = 'Attendance This Month';

    protected function getData(): array
    {
        [$start, $end] = $this->getMonthRange();

        $rows = Attendance::query()
            ->join('college_classes', 'attendances.college_class_id', '=', 'college_classes.id')
            ->whereBetween('attendances.attendance_date', [$start, $end])
            ->groupBy('attendances.college_class_id', 'college_classes.name')
            ->orderBy('college_classes.name')
            ->selectRaw('college_classes.name as class_name')
            ->selectRaw("SUM(CASE WHEN attendances.status IN ('present', 'late', 'excused') THEN 1 ELSE 0 END) as present_count")
            ->selectRaw("SUM(CASE WHEN attendances.status = 'absent' THEN 1 ELSE 0 END) as absent_count")
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Present',
                    'data' => $rows->pluck('present_count')->map(fn ($value): int => (int) $value)->all(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.7)',
                    'borderColor' => 'rgb(16, 185, 129)',
                ],
                [
                    'label' => 'Absent',
                    'data' => $rows->pluck('absent_count')->map(fn ($value): int => (int) $value)->all(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                    'borderColor' => 'rgb(239, 68, 68)',
                ],
            ],
            'labels' => $rows->pluck('class_name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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

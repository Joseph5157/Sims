<x-filament-panels::page>
    <style>
        @media print {
            .fi-sidebar,
            .fi-topbar,
            .fi-header-actions {
                display: none !important;
            }

            body {
                background: white;
            }
        }
    </style>

    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Filters</x-slot>
            {{ $this->form }}
        </x-filament::section>

        @if (($studentName ?? null) && ($rollNumber ?? null) && ($className ?? null))
            <x-filament::section heading="Student Report Card">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Student</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $studentName }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Roll Number</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $rollNumber }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Class</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $className }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Department</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $departmentName ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Attendance</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $attendancePercentage }}%</p>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section heading="Marks">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-3 py-2 text-left">Subject</th>
                                <th class="px-3 py-2 text-left">Max Marks</th>
                                <th class="px-3 py-2 text-left">Marks Obtained</th>
                                <th class="px-3 py-2 text-left">Percentage</th>
                                <th class="px-3 py-2 text-left">Grade Letter</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($marks as $row)
                                <tr class="border-b border-gray-100 dark:border-gray-700 last:border-0">
                                    <td class="px-3 py-2">{{ $row['subject']->name ?? 'N/A' }}</td>
                                    <td class="px-3 py-2">{{ number_format($row['max_marks'], 2) }}</td>
                                    <td class="px-3 py-2">{{ number_format($row['obtained'], 2) }}</td>
                                    <td class="px-3 py-2">{{ $row['percentage'] }}%</td>
                                    <td class="px-3 py-2">{{ $row['grade'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-3 py-4 text-gray-500" colspan="5">No marks found for this selection.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>

            <x-filament::section heading="Summary">
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Total Obtained / Total Max</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($totalObtained, 2) }} / {{ number_format($totalMax, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Overall %</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $overallPercentage }}%</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Overall Grade</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $overallGrade ?? '-' }}</p>
                    </div>
                </div>
            </x-filament::section>
        @elseif (($data['student_id'] ?? null) && ($data['exam_group_id'] ?? null))
            <x-filament::section>
                <p class="py-8 text-center text-gray-500">No report card data found for this selection.</p>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Attendance Overview -->
        <x-filament::section heading="Attendance Overview">
            @if($student)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Attendance</p>
                        <p class="text-4xl font-semibold mt-1">
                            {{ $student->getAttendancePercentage() }}<span class="text-2xl">%</span>
                        </p>
                    </div>
                    <div class="text-5xl">
                        @if($student->getAttendancePercentage() < 50)
                            <span class="inline-block px-3 py-1 text-xs font-semibold text-white bg-red-500 rounded-full">Low</span>
                        @elseif($student->getAttendancePercentage() < 75)
                            <span class="inline-block px-3 py-1 text-xs font-semibold text-white bg-yellow-500 rounded-full">Moderate</span>
                        @else
                            <span class="inline-block px-3 py-1 text-xs font-semibold text-white bg-green-500 rounded-full">Good</span>
                        @endif
                    </div>
                </div>
                <div class="mt-4 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div
                        class="h-full rounded-full transition-all duration-300
                            @if($student->getAttendancePercentage() < 50)
                                bg-red-500
                            @elseif($student->getAttendancePercentage() < 75)
                                bg-yellow-500
                            @else
                                bg-emerald-500
                            @endif"
                        style="width: {{ $student->getAttendancePercentage() }}%">
                    </div>
                </div>
            @else
                <p class="text-muted-foreground py-8 text-center">No attendance data available.</p>
            @endif
        </x-filament::section>

        <!-- Recent Grades -->
        <x-filament::section heading="Recent Grades">
            @if($recentGrades->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-3 px-4">Subject</th>
                                <th class="text-left py-3 px-4">Exam Type</th>
                                <th class="text-right py-3 px-4">Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentGrades as $grade)
                                <tr class="border-b dark:border-gray-700 last:border-none">
                                    <td class="py-3 px-4">{{ $grade->subject?->name ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">{{ ucfirst(str_replace('_', ' ', $grade->exam_type)) }}</td>
                                    <td class="py-3 px-4 text-right font-medium">
                                        {{ $grade->marks_obtained }} / {{ $grade->total_marks }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted-foreground py-8 text-center">No grades recorded yet.</p>
            @endif
        </x-filament::section>

        <!-- Recent Notices -->
        <x-filament::section heading="Recent Notices">
            @if($recentNotices->isNotEmpty())
                <div class="space-y-4">
                    @foreach($recentNotices as $notice)
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-3 mb-3 last:border-0">
                            <h4 class="font-medium text-lg">{{ $notice->title }}</h4>
                            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $notice->content }}</p>
                            <p class="text-xs text-muted-foreground mt-3">Expires: {{ $notice->expiry_date ? $notice->expiry_date->format('d M Y') : 'N/A' }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted-foreground py-8 text-center">No notices at this time.</p>
            @endif
        </x-filament::section>

        <!-- Today's Timetable -->
        <x-filament::section heading="Today's Timetable">
            @if($todayTimetable->isNotEmpty())
                <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-3 px-4 text-left">Period</th>
                            <th class="py-3 px-4 text-left">Subject</th>
                            <th class="py-3 px-4 text-left">Faculty</th>
                            <th class="py-3 px-4 text-left">Room</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todayTimetable as $slot)
                            <tr class="border-b border-gray-200 dark:border-gray-700 last:border-0">
                                <td class="py-1 pr-6">{{ $slot->period }}</td>
                                <td class="py-1 pr-6">{{ $slot->subject?->name ?? 'N/A' }}</td>
                                <td class="py-1 pr-6">{{ $slot->faculty?->user?->name ?? 'N/A' }}</td>
                                <td class="py-1 pr-6">{{ $slot->room ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            @else
                <p class="text-muted-foreground py-8 text-center">No classes scheduled for today.</p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>

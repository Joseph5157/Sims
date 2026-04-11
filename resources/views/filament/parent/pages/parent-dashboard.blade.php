<x-filament-panels::page>
    @if ($student === null)
        <x-filament::section>
            <x-slot name="heading">Student Overview</x-slot>

            <p class="py-8 text-center text-muted-foreground">
                No student profile linked to your account.
            </p>
        </x-filament::section>
    @else
        @php
            $parentName = auth()->user()?->name ?? 'Parent';
            $studentName = $student->user->name;
            $studentClass = $student->collegeClass?->name ?? 'N/A';
            $attendancePercentage = $attendancePercentage ?? $student->getAttendancePercentage();
            $totalGradesCount = $student->grades->count();
            $activeNoticesCount = $activeNotices->count();
            $attendanceColorClass = $attendancePercentage >= 75
                ? 'text-emerald-600 bg-emerald-50 border-emerald-200 dark:text-emerald-300 dark:bg-emerald-500/10 dark:border-emerald-500/20'
                : ($attendancePercentage >= 50
                    ? 'text-amber-600 bg-amber-50 border-amber-200 dark:text-amber-300 dark:bg-amber-500/10 dark:border-amber-500/20'
                    : 'text-red-600 bg-red-50 border-red-200 dark:text-red-300 dark:bg-red-500/10 dark:border-red-500/20');
            $attendanceBarClass = $attendancePercentage >= 75
                ? 'bg-emerald-500'
                : ($attendancePercentage >= 50 ? 'bg-amber-500' : 'bg-red-500');
        @endphp

        <div class="space-y-6">
            <div class="rounded-xl border border-gray-200 bg-gray-900 text-white shadow-sm dark:border-gray-700">
                <div class="border-l-4 border-amber-400 p-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-[0.2em] text-white/60">
                                Parent Portal
                            </p>
                            <h1 class="mt-2 text-3xl font-semibold tracking-tight">
                                {{ $parentName }}
                            </h1>
                        </div>

                        <div class="text-left md:text-right">
                            <p class="text-sm text-white/60">Student</p>
                            <p class="mt-1 text-xl font-semibold">{{ $studentName }}</p>
                            <p class="text-sm text-white/70">{{ $studentClass }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full {{ $attendanceColorClass }}">
                            <x-filament::icon icon="heroicon-o-chart-bar-square" class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-2xl font-semibold">
                                {{ $attendancePercentage }}%
                            </p>
                            <p class="text-sm text-gray-400 dark:text-gray-300">Attendance</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-300">
                            <x-filament::icon icon="heroicon-o-academic-cap" class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-2xl font-semibold">{{ $totalGradesCount }}</p>
                            <p class="text-sm text-gray-400 dark:text-gray-300">Total Grades</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300">
                            <x-filament::icon icon="heroicon-o-megaphone" class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-2xl font-semibold">{{ $activeNoticesCount }}</p>
                            <p class="text-sm text-gray-400 dark:text-gray-300">Active Notices</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-filament::section>
                    <x-slot name="heading">Recent Grades</x-slot>

                    @if ($recentGrades->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700">
                                        <th class="py-3 px-4 text-left font-medium text-gray-700 dark:text-gray-200">Subject</th>
                                        <th class="py-3 px-4 text-left font-medium text-gray-700 dark:text-gray-200">Exam Type</th>
                                        <th class="py-3 px-4 text-left font-medium text-gray-700 dark:text-gray-200">Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentGrades as $grade)
                                        @php
                                            $gradePercentage = $grade->total_marks > 0 ? ($grade->marks_obtained / $grade->total_marks) * 100 : 0;
                                            $gradeBadgeClass = $gradePercentage > 75
                                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:border-emerald-500/20'
                                                : ($gradePercentage > 50
                                                    ? 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:border-amber-500/20'
                                                    : 'bg-red-50 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-300 dark:border-red-500/20');
                                        @endphp

                                        <tr class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-800 dark:even:bg-gray-700/40">
                                            <td class="py-1 pr-8 font-medium">
                                                {{ $grade->subject?->name ?? 'N/A' }}
                                            </td>
                                            <td class="py-1 pr-8">
                                                {{ ucfirst(str_replace('_', ' ', $grade->exam_type)) }}
                                            </td>
                                            <td class="py-1 pr-8">
                                                <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $gradeBadgeClass }}">
                                                    {{ $grade->marks_obtained }} / {{ $grade->total_marks }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="py-8 text-center text-muted-foreground">No grades recorded yet.</p>
                    @endif
                </x-filament::section>

                <x-filament::section>
                    <x-slot name="heading">Active Notices</x-slot>

                    @if ($activeNotices->isNotEmpty())
                        <div class="space-y-4">
                            @foreach ($activeNotices as $notice)
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                    <div class="flex items-start justify-between gap-4">
                                        <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $notice->title }}
                                        </h4>
                                    </div>

                                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                                        {{ $notice->content }}
                                    </p>

                                    <div class="mt-4 flex justify-end">
                                        <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">
                                        Expires: {{ $notice->expires_at ? $notice->expires_at->format('d M Y') : 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="py-8 text-center text-muted-foreground">No notices at this time.</p>
                    @endif
                </x-filament::section>
            </div>

            <x-filament::section>
                <x-slot name="heading">Today's Timetable</x-slot>

                @if ($todayTimetable->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th class="py-3 px-4 text-left font-medium text-gray-700 dark:text-gray-200">Period</th>
                                    <th class="py-3 px-4 text-left font-medium text-gray-700 dark:text-gray-200">Subject</th>
                                    <th class="py-3 px-4 text-left font-medium text-gray-700 dark:text-gray-200">Faculty</th>
                                    <th class="py-3 px-4 text-left font-medium text-gray-700 dark:text-gray-200">Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($todayTimetable as $slot)
                                    <tr class="border-b border-gray-100 odd:bg-white even:bg-gray-50 dark:border-gray-700 dark:odd:bg-gray-800 dark:even:bg-gray-700/40 last:border-0">
                                        <td class="py-1 pr-8 font-medium">{{ $slot->period }}</td>
                                        <td class="py-1 pr-8">{{ $slot->subject?->name ?? 'N/A' }}</td>
                                        <td class="py-1 pr-8">{{ $slot->faculty?->user?->name ?? 'N/A' }}</td>
                                        <td class="py-1 pr-8">{{ $slot->room ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-center text-muted-foreground">
                        <x-filament::icon icon="heroicon-o-calendar-days" class="h-10 w-10 text-gray-400 dark:text-gray-500" />
                        <p class="mt-4 text-sm font-medium">No classes scheduled for today</p>
                    </div>
                @endif
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>

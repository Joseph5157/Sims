<x-filament-panels::page>
@if (! $hasProfile)
    <div class="flex flex-col items-center justify-center gap-4 py-24 text-center">
        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-violet-100 dark:bg-violet-900/30">
            <x-heroicon-o-user-circle class="h-10 w-10 text-violet-500" />
        </div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">No Student Profile Found</h2>
        <p class="max-w-sm text-sm text-gray-500 dark:text-gray-400">
            Your account is not linked to a student profile yet. Please contact the administrator.
        </p>
    </div>
@else
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="col-span-full overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-violet-600 to-purple-700 shadow-lg">
            <div class="relative px-6 py-6">
                <div class="pointer-events-none absolute -right-8 -top-8 h-40 w-40 rounded-full bg-white/5"></div>
                <div class="pointer-events-none absolute -bottom-6 right-20 h-24 w-24 rounded-full bg-white/5"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-violet-200">
                            {{ $schoolName }}
                        </p>
                        <h1 class="mt-1.5 text-2xl font-bold text-white sm:text-3xl">
                            Hello, {{ $profile['name'] }}!
                        </h1>
                        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-violet-200">
                            <span>{{ $profile['class'] }}</span>
                            <span class="opacity-50">•</span>
                            <span>Roll {{ $profile['roll_number'] }}</span>
                            @if ($profile['admission_no'] !== '—')
                                <span class="opacity-50">•</span>
                                <span>{{ $profile['admission_no'] }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-3 sm:flex-col sm:items-end sm:gap-1">
                        <div class="rounded-xl bg-white/15 px-4 py-2 text-center backdrop-blur-sm">
                            <p class="text-xs text-violet-200">Academic Year</p>
                            <p class="text-lg font-bold text-white">{{ $profile['academic_year'] }}</p>
                        </div>
                        <span class="rounded-full bg-green-400/20 px-3 py-1 text-xs font-semibold text-green-200">
                            {{ $profile['status'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
            <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/30">
                    <x-heroicon-o-chart-bar-square class="h-4 w-4 text-violet-600 dark:text-violet-400" />
                </div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Attendance</h3>
                <span class="ml-auto rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $attendance['label_color'] === 'text-green-600 dark:text-green-400' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($attendance['label_color'] === 'text-yellow-600 dark:text-yellow-400' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">
                    {{ $attendance['label'] }}
                </span>
            </div>

            <div class="flex flex-1 flex-col items-center justify-center gap-4 p-5">
                <div class="relative flex items-center justify-center">
                    <svg class="h-36 w-36 -rotate-90" viewBox="0 0 100 100">
                        <circle
                            cx="50" cy="50" r="44"
                            fill="none"
                            stroke-width="8"
                            class="stroke-gray-100 dark:stroke-gray-800"
                        />
                        <circle
                            cx="50" cy="50" r="44"
                            fill="none"
                            stroke-width="8"
                            stroke-linecap="round"
                            style="
                                stroke: {{ $attendance['ring_color'] }};
                                stroke-dasharray: {{ $attendance['circumference'] }};
                                stroke-dashoffset: {{ $attendance['dash_offset'] }};
                                transition: stroke-dashoffset 0.6s ease;
                            "
                        />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $attendance['percentage'] }}<span class="text-lg">%</span>
                        </span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">overall</span>
                    </div>
                </div>

                <div class="grid w-full grid-cols-3 divide-x divide-gray-100 rounded-xl bg-gray-50 dark:divide-gray-800 dark:bg-gray-800/50">
                    <div class="py-3 text-center">
                        <div class="text-xl font-bold text-green-600 dark:text-green-400">{{ $attendance['present'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Present</div>
                    </div>
                    <div class="py-3 text-center">
                        <div class="text-xl font-bold text-red-600 dark:text-red-400">{{ $attendance['absent'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Absent</div>
                    </div>
                    <div class="py-3 text-center">
                        <div class="text-xl font-bold text-gray-700 dark:text-gray-300">{{ $attendance['total'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Total Days</div>
                    </div>
                </div>

                @if ($attendance['late'] > 0 || $attendance['excused'] > 0)
                    <div class="flex w-full justify-center gap-4 text-xs text-gray-400 dark:text-gray-500">
                        @if ($attendance['late'] > 0)
                            <span>Late: <strong class="text-yellow-600 dark:text-yellow-400">{{ $attendance['late'] }}</strong></span>
                        @endif
                        @if ($attendance['excused'] > 0)
                            <span>Excused: <strong class="text-blue-600 dark:text-blue-400">{{ $attendance['excused'] }}</strong></span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
            <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/30">
                    <x-heroicon-o-academic-cap class="h-4 w-4 text-violet-600 dark:text-violet-400" />
                </div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Latest Results</h3>
            </div>

            <div class="flex flex-1 flex-col justify-center p-5">
                @if (count($results) > 0)
                    <div class="space-y-4">
                        @foreach ($results as $result)
                            <div>
                                <div class="mb-1.5 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $result['name'] }}
                                        </span>
                                        @if ($result['type'])
                                            <span class="rounded px-1.5 py-0.5 text-xs font-medium {{ $result['type'] === 'FA' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' }}">
                                                {{ $result['type'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $result['percentage'] }}%</span>
                                        <span class="inline-block rounded px-2 py-0.5 text-xs font-bold {{ $result['grade_class'] }}">
                                            {{ $result['grade'] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="h-1.5 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div
                                        class="h-full rounded-full {{ $result['pct_bar_color'] }}"
                                        style="width: {{ $result['percentage'] }}%"
                                    ></div>
                                </div>
                                @if ($result['date'] !== '—')
                                    <p class="mt-0.5 text-right text-xs text-gray-400 dark:text-gray-500">{{ $result['date'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center gap-2 py-8 text-center">
                        <x-heroicon-o-document-text class="h-8 w-8 text-gray-300 dark:text-gray-600" />
                        <p class="text-sm text-gray-400 dark:text-gray-500">No published results yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
            <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/30">
                    <x-heroicon-o-megaphone class="h-4 w-4 text-violet-600 dark:text-violet-400" />
                </div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notices</h3>
            </div>

            <div class="flex flex-1 flex-col justify-center p-5">
                @if (count($notices) > 0)
                    <div class="space-y-3">
                        @foreach ($notices as $notice)
                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/50">
                                <div class="flex items-start justify-between gap-3">
                                    <h4 class="text-sm font-semibold leading-snug text-gray-900 dark:text-gray-100">
                                        {{ $notice['title'] }}
                                    </h4>
                                    <span class="mt-0.5 flex-shrink-0 text-xs text-gray-400 dark:text-gray-500">
                                        {{ $notice['date'] }}
                                    </span>
                                </div>
                                @if ($notice['body'])
                                    <p class="mt-1.5 text-xs leading-relaxed text-gray-500 dark:text-gray-400">
                                        {{ $notice['body'] }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center gap-2 py-8 text-center">
                        <x-heroicon-o-bell-slash class="h-8 w-8 text-gray-300 dark:text-gray-600" />
                        <p class="text-sm text-gray-400 dark:text-gray-500">No notices at this time.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
            <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/30">
                    <x-heroicon-o-banknotes class="h-4 w-4 text-violet-600 dark:text-violet-400" />
                </div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fee Status</h3>
                <span class="ml-auto rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $feeStatus['status_class'] }}">
                    {{ $feeStatus['status_label'] }}
                </span>
            </div>

            <div class="flex flex-1 flex-col justify-center p-5">
                @if ($feeStatus['is_paid'])
                    <div class="flex flex-col items-center justify-center gap-3 py-4 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                            <x-heroicon-o-check-circle class="h-8 w-8 text-green-600 dark:text-green-400" />
                        </div>
                        <div>
                            <p class="text-lg font-bold text-green-700 dark:text-green-400">All Fees Paid</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500">No outstanding amount.</p>
                        </div>
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="rounded-xl {{ $feeStatus['is_overdue'] ? 'bg-red-50 dark:bg-red-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20' }} p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Outstanding Amount</p>
                            <p class="mt-1 text-3xl font-bold {{ $feeStatus['is_overdue'] ? 'text-red-700 dark:text-red-400' : 'text-yellow-700 dark:text-yellow-400' }}">
                                ₹{{ number_format($feeStatus['outstanding'], 2) }}
                            </p>
                        </div>
                        <div class="flex items-center justify-between rounded-lg border border-gray-100 px-4 py-3 dark:border-gray-800">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Due Date</span>
                            <span class="text-sm font-semibold {{ $feeStatus['is_overdue'] ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">
                                {{ $feeStatus['due_date'] }}
                                @if ($feeStatus['is_overdue'])
                                    <span class="ml-1 text-xs">(Overdue)</span>
                                @endif
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-span-full flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700 md:col-span-1">
            <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/30">
                    <x-heroicon-o-calendar-days class="h-4 w-4 text-violet-600 dark:text-violet-400" />
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Today's Schedule</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ now()->format('l, d F Y') }}</p>
                </div>
            </div>

            <div class="flex-1 p-5">
                @if (count($todaySchedule) > 0)
                    <div class="space-y-2">
                        @foreach ($todaySchedule as $slot)
                            @php
                                $typeColors = [
                                    'theory' => 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-700',
                                    'practical' => 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-700',
                                    'elective' => 'bg-purple-50 border-purple-200 dark:bg-purple-900/20 dark:border-purple-700',
                                    'project' => 'bg-orange-50 border-orange-200 dark:bg-orange-900/20 dark:border-orange-700',
                                ];
                                $cardBg = $slot['is_current']
                                    ? 'bg-violet-50 border-violet-300 dark:bg-violet-900/20 dark:border-violet-600'
                                    : ($typeColors[$slot['subject_type'] ?? ''] ?? 'bg-gray-50 border-gray-200 dark:bg-gray-800/60 dark:border-gray-700');
                            @endphp
                            <div class="flex items-center gap-4 rounded-xl border p-3.5 {{ $cardBg }}">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg {{ $slot['is_current'] ? 'bg-violet-600' : 'bg-white dark:bg-gray-900' }} shadow-sm">
                                    <span class="text-sm font-bold {{ $slot['is_current'] ? 'text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                        {{ $slot['period'] }}
                                    </span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $slot['subject'] }}
                                        @if ($slot['is_current'])
                                            <span class="ml-1.5 inline-flex items-center rounded-full bg-violet-600 px-2 py-0.5 text-xs font-medium text-white">Now</span>
                                        @endif
                                    </p>
                                    <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $slot['faculty'] }}</p>
                                </div>

                                @if ($slot['start_time'])
                                    <div class="flex-shrink-0 text-right text-xs text-gray-500 dark:text-gray-400">
                                        <div class="font-medium text-gray-700 dark:text-gray-300">{{ $slot['start_time'] }}</div>
                                        @if ($slot['end_time'])
                                            <div>{{ $slot['end_time'] }}</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center gap-3 py-8 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                            <x-heroicon-o-sun class="h-8 w-8 text-gray-400 dark:text-gray-500" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">No classes today</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Enjoy your free day!</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
</x-filament-panels::page>

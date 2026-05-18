<x-filament-panels::page>

{{-- ========================================================================== --}}
{{-- NO PROFILE STATE                                                            --}}
{{-- ========================================================================== --}}
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

{{-- ========================================================================== --}}
{{-- PAGE HEADER                                                                 --}}
{{-- ========================================================================== --}}
<div class="overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-violet-600 to-purple-700 shadow-lg">
    <div class="relative px-6 py-5">
        <div class="pointer-events-none absolute -right-8 -top-8 h-40 w-40 rounded-full bg-white/5"></div>
        <div class="pointer-events-none absolute -bottom-6 right-24 h-24 w-24 rounded-full bg-white/5"></div>
        <div class="relative flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/15 backdrop-blur-sm">
                <x-heroicon-o-calendar-days class="h-6 w-6 text-white" />
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">Attendance Record</h1>
                <p class="text-sm text-violet-200">Track your daily & monthly attendance</p>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================== --}}
{{-- TAB SWITCHER                                                                --}}
{{-- ========================================================================== --}}
<div class="mt-4 flex gap-1 rounded-xl bg-gray-100 p-1 dark:bg-gray-800/60">
    <button
        wire:click="switchTab('yearly')"
        class="flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium transition-all duration-150
            {{ $activeTab === 'yearly'
                ? 'bg-white text-violet-600 shadow-sm dark:bg-gray-700 dark:text-violet-400'
                : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}"
    >
        <x-heroicon-o-chart-bar class="h-4 w-4" />
        Yearly Overview
    </button>
    <button
        wire:click="switchTab('monthly')"
        class="flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium transition-all duration-150
            {{ $activeTab === 'monthly'
                ? 'bg-white text-violet-600 shadow-sm dark:bg-gray-700 dark:text-violet-400'
                : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}"
    >
        <x-heroicon-o-calendar class="h-4 w-4" />
        Monthly Calendar
    </button>
</div>

{{-- ========================================================================== --}}
{{-- YEARLY VIEW                                                                 --}}
{{-- ========================================================================== --}}
@if ($activeTab === 'yearly')

    @if (! $hasRecords)
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center gap-4 rounded-2xl bg-white py-20 text-center shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                <x-heroicon-o-calendar-days class="h-8 w-8 text-gray-400 dark:text-gray-500" />
            </div>
            <div>
                <p class="text-base font-semibold text-gray-700 dark:text-gray-300">No attendance records yet</p>
                <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">Records will appear here once attendance is marked.</p>
            </div>
        </div>

    @else

        {{-- ------------------------------------------------------------------ --}}
        {{-- CIRCULAR PROGRESS RING                                              --}}
        {{-- ------------------------------------------------------------------ --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
            <div class="flex flex-col items-center gap-4 px-6 py-8">

                {{-- Ring --}}
                <div class="relative flex items-center justify-center">
                    <svg class="h-52 w-52 -rotate-90" viewBox="0 0 100 100">
                        {{-- Track --}}
                        <circle
                            cx="50" cy="50" r="44"
                            fill="none"
                            stroke-width="8"
                            class="stroke-gray-100 dark:stroke-gray-800"
                        />
                        {{-- Progress --}}
                        <circle
                            cx="50" cy="50" r="44"
                            fill="none"
                            stroke-width="8"
                            stroke-linecap="round"
                            style="
                                stroke: {{ $yearlyStats['ring_color'] }};
                                stroke-dasharray: {{ $yearlyStats['circumference'] }};
                                stroke-dashoffset: {{ $yearlyStats['dash_offset'] }};
                                transition: stroke-dashoffset 0.8s ease;
                            "
                        />
                    </svg>
                    {{-- Centre text --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100">
                            {{ $yearlyStats['percentage'] }}<span class="text-xl font-bold">%</span>
                        </span>
                        <span class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">attendance</span>
                    </div>
                </div>

                {{-- Status badge --}}
                <span class="rounded-full px-4 py-1.5 text-sm font-semibold {{ $yearlyStats['label_bg'] }}">
                    {{ $yearlyStats['label'] }}
                </span>

            </div>
        </div>

        {{-- ------------------------------------------------------------------ --}}
        {{-- STAT CARDS                                                          --}}
        {{-- ------------------------------------------------------------------ --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">

            {{-- Present --}}
            <div class="flex flex-col items-center gap-1.5 rounded-2xl bg-white px-4 py-5 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                    <x-heroicon-o-check-circle class="h-5 w-5 text-green-600 dark:text-green-400" />
                </div>
                <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $yearlyStats['present'] }}</span>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Present</span>
            </div>

            {{-- Absent --}}
            <div class="flex flex-col items-center gap-1.5 rounded-2xl bg-white px-4 py-5 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <x-heroicon-o-x-circle class="h-5 w-5 text-red-600 dark:text-red-400" />
                </div>
                <span class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $yearlyStats['absent'] }}</span>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Absent</span>
            </div>

            {{-- Late --}}
            <div class="flex flex-col items-center gap-1.5 rounded-2xl bg-white px-4 py-5 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                    <x-heroicon-o-clock class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                </div>
                <span class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $yearlyStats['late'] }}</span>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Late</span>
            </div>

            {{-- Excused --}}
            <div class="flex flex-col items-center gap-1.5 rounded-2xl bg-white px-4 py-5 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                    <x-heroicon-o-shield-check class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $yearlyStats['excused'] }}</span>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Excused</span>
            </div>

            {{-- Working Days --}}
            <div class="col-span-2 flex flex-col items-center gap-1.5 rounded-2xl bg-white px-4 py-5 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700 sm:col-span-1">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                    <x-heroicon-o-calendar class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                </div>
                <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ $yearlyStats['working_days'] }}</span>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Working Days</span>
            </div>

        </div>

        {{-- ------------------------------------------------------------------ --}}
        {{-- MONTH-WISE SUMMARY TABLE                                            --}}
        {{-- ------------------------------------------------------------------ --}}
        @if (count($monthlyBreakdown) > 0)
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">

                <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/30">
                        <x-heroicon-o-table-cells class="h-4 w-4 text-violet-600 dark:text-violet-400" />
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Month-wise Breakdown</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[520px] text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Month</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Working Days</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-green-600 dark:text-green-400">Present</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-red-600 dark:text-red-400">Absent</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-yellow-600 dark:text-yellow-400">Late</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @foreach ($monthlyBreakdown as $row)
                                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/40">
                                    <td class="px-5 py-3.5 font-medium text-gray-900 dark:text-gray-100">{{ $row['month'] }}</td>
                                    <td class="px-4 py-3.5 text-center text-gray-600 dark:text-gray-400">{{ $row['working_days'] }}</td>
                                    <td class="px-4 py-3.5 text-center font-medium text-green-600 dark:text-green-400">{{ $row['present'] }}</td>
                                    <td class="px-4 py-3.5 text-center font-medium text-red-600 dark:text-red-400">{{ $row['absent'] }}</td>
                                    <td class="px-4 py-3.5 text-center font-medium text-yellow-600 dark:text-yellow-400">{{ $row['late'] }}</td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="font-bold {{ $row['pct_class'] }}">{{ $row['percentage'] }}%</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        @endif

    @endif
@endif

{{-- ========================================================================== --}}
{{-- MONTHLY CALENDAR VIEW                                                       --}}
{{-- ========================================================================== --}}
@if ($activeTab === 'monthly')

    {{-- ------------------------------------------------------------------ --}}
    {{-- MONTH / YEAR SELECTORS                                              --}}
    {{-- ------------------------------------------------------------------ --}}
    <div class="flex gap-3">

        {{-- Month --}}
        <div class="flex-1">
            <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Month</label>
            <select
                wire:model.live="selectedMonth"
                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm font-medium text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-violet-500 dark:focus:ring-violet-900/30"
            >
                @foreach (range(1, 12) as $m)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}</option>
                @endforeach
            </select>
        </div>

        {{-- Year --}}
        <div class="w-28">
            <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Year</label>
            <select
                wire:model.live="selectedYear"
                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm font-medium text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-violet-500 dark:focus:ring-violet-900/30"
            >
                @foreach (range(now()->year - 3, now()->year) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>

    </div>

    {{-- ------------------------------------------------------------------ --}}
    {{-- CALENDAR CARD                                                       --}}
    {{-- ------------------------------------------------------------------ --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">

        {{-- Calendar header --}}
        <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/30">
                <x-heroicon-o-calendar class="h-4 w-4 text-violet-600 dark:text-violet-400" />
            </div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $calendarTitle }}</h3>
        </div>

        <div class="p-4 sm:p-5">

            {{-- Day-of-week headers --}}
            <div class="mb-1 grid grid-cols-7 gap-1">
                @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dow)
                    <div class="py-1.5 text-center text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">
                        {{ $dow }}
                    </div>
                @endforeach
            </div>

            {{-- Calendar grid --}}
            <div class="grid grid-cols-7 gap-1">

                {{-- Padding cells before day 1 --}}
                @for ($i = 0; $i < $startPadding; $i++)
                    <div class="aspect-square rounded-lg"></div>
                @endfor

                {{-- Day cells --}}
                @foreach ($calendarDays as $day)
                    @php
                        $s = $day['status'];

                        // Cell background + text based on status
                        if ($day['is_weekend']) {
                            $cellBg   = 'bg-gray-50 dark:bg-gray-800/40';
                            $textCls  = 'text-gray-400 dark:text-gray-600';
                            $dotColor = '';
                        } elseif ($day['is_future']) {
                            $cellBg   = '';
                            $textCls  = 'text-gray-300 dark:text-gray-700';
                            $dotColor = '';
                        } elseif ($s === 'present') {
                            $cellBg   = 'bg-green-50 dark:bg-green-900/20';
                            $textCls  = 'text-green-700 dark:text-green-400';
                            $dotColor = 'bg-green-500';
                        } elseif ($s === 'absent') {
                            $cellBg   = 'bg-red-50 dark:bg-red-900/20';
                            $textCls  = 'text-red-700 dark:text-red-400';
                            $dotColor = 'bg-red-500';
                        } elseif ($s === 'late') {
                            $cellBg   = 'bg-yellow-50 dark:bg-yellow-900/20';
                            $textCls  = 'text-yellow-700 dark:text-yellow-400';
                            $dotColor = 'bg-yellow-500';
                        } elseif ($s === 'excused') {
                            $cellBg   = 'bg-blue-50 dark:bg-blue-900/20';
                            $textCls  = 'text-blue-700 dark:text-blue-400';
                            $dotColor = 'bg-blue-500';
                        } else {
                            $cellBg   = '';
                            $textCls  = 'text-gray-600 dark:text-gray-400';
                            $dotColor = '';
                        }

                        $todayRing = $day['is_today']
                            ? ' ring-2 ring-violet-500 ring-offset-1 dark:ring-offset-gray-900'
                            : '';
                    @endphp

                    <div class="relative flex aspect-square flex-col items-center justify-center rounded-lg {{ $cellBg }}{{ $todayRing }}">
                        <span class="text-xs font-semibold {{ $textCls }}">{{ $day['day'] }}</span>
                        @if ($dotColor)
                            <div class="mt-0.5 h-1.5 w-1.5 rounded-full {{ $dotColor }}"></div>
                        @elseif ($day['is_weekend'])
                            <div class="mt-0.5 h-1.5 w-1.5 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                        @endif
                    </div>

                @endforeach

            </div>

        </div>
    </div>

    {{-- ------------------------------------------------------------------ --}}
    {{-- LEGEND                                                              --}}
    {{-- ------------------------------------------------------------------ --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
        <div class="px-5 py-4">
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Legend</p>
            <div class="flex flex-wrap gap-x-5 gap-y-2">
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded-full bg-green-500"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Present</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded-full bg-red-500"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Absent</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded-full bg-yellow-500"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Late</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Excused</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Weekend</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded-full ring-2 ring-violet-500"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Today</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ------------------------------------------------------------------ --}}
    {{-- MONTHLY STATS                                                       --}}
    {{-- ------------------------------------------------------------------ --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">

        <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/30">
                <x-heroicon-o-chart-bar-square class="h-4 w-4 text-violet-600 dark:text-violet-400" />
            </div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $calendarTitle }} — Summary</h3>
            @if ($monthStats['working_days'] > 0)
                <span class="ml-auto rounded-full px-2.5 py-1 text-xs font-semibold {{ $monthStats['label_bg'] }}">
                    {{ $monthStats['percentage'] }}%
                </span>
            @endif
        </div>

        @if ($monthStats['working_days'] === 0)
            <div class="flex flex-col items-center justify-center gap-3 py-10 text-center">
                <x-heroicon-o-calendar-days class="h-8 w-8 text-gray-300 dark:text-gray-600" />
                <p class="text-sm text-gray-400 dark:text-gray-500">No attendance marked for this month.</p>
            </div>
        @else
            <div class="grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-800 sm:grid-cols-5">

                <div class="flex flex-col items-center gap-1 py-5">
                    <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $monthStats['present'] }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Present</span>
                </div>

                <div class="flex flex-col items-center gap-1 py-5">
                    <span class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $monthStats['absent'] }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Absent</span>
                </div>

                <div class="flex flex-col items-center gap-1 py-5">
                    <span class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $monthStats['late'] }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Late</span>
                </div>

                <div class="col-span-3 flex flex-col items-center gap-1 border-t border-gray-100 py-5 dark:border-gray-800 sm:col-span-1 sm:border-t-0">
                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $monthStats['excused'] }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Excused</span>
                </div>

                <div class="col-span-3 flex flex-col items-center gap-1 border-t border-gray-100 py-5 dark:border-gray-800 sm:col-span-1 sm:border-t-0">
                    <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ $monthStats['working_days'] }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Days Marked</span>
                </div>

            </div>
        @endif

    </div>

@endif
{{-- end monthly tab --}}

@endif
{{-- end hasProfile --}}

</x-filament-panels::page>

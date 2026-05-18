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
                <x-heroicon-o-clock class="h-6 w-6 text-white" />
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">My Timetable</h1>
                <p class="text-sm text-violet-200">
                    {{ $profile['name'] }}
                    <span class="opacity-60">·</span>
                    {{ $profile['class'] }}
                </p>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================== --}}
{{-- TODAY BANNER                                                                --}}
{{-- ========================================================================== --}}
<div class="flex items-center gap-3 rounded-xl bg-violet-50 px-5 py-3.5 dark:bg-violet-900/20">
    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/40">
        <x-heroicon-o-calendar-days class="h-4 w-4 text-violet-600 dark:text-violet-400" />
    </div>
    <p class="text-sm font-medium text-violet-800 dark:text-violet-200">
        Today is
        <span class="font-bold">{{ $todayLabel }}</span>
        <span class="ml-2 text-xs text-violet-500 dark:text-violet-400">
            {{ now()->format('d F Y') }}
        </span>
    </p>
</div>

{{-- ========================================================================== --}}
{{-- FULL EMPTY STATE (no slots at all)                                          --}}
{{-- ========================================================================== --}}
@if (! $hasSlots)
    <div class="flex flex-col items-center justify-center gap-4 rounded-2xl bg-white py-20 text-center shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
            <x-heroicon-o-clock class="h-8 w-8 text-gray-400 dark:text-gray-500" />
        </div>
        <div>
            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Timetable not assigned yet</p>
            <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">
                Your class timetable will appear here once it has been set up by admin.
            </p>
        </div>
    </div>

@else

{{-- ========================================================================== --}}
{{-- WEEKLY TIMETABLE                                                            --}}
{{-- ========================================================================== --}}
<div class="space-y-3">
    @foreach ($timetable as $day)

        @php
            $isToday     = $day['is_today'];
            $hasSlots    = count($day['slots']) > 0;
            $isSunday    = false; // DayOfWeek only goes Mon–Sat, no Sunday
        @endphp

        {{-- Day section card --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm
            {{ $isToday
                ? 'ring-2 ring-violet-400 dark:ring-violet-500'
                : 'ring-1 ring-gray-200 dark:ring-gray-700' }}
            dark:bg-gray-900">

            {{-- ---------------------------------------------------------------- --}}
            {{-- DAY HEADER                                                        --}}
            {{-- ---------------------------------------------------------------- --}}
            <div class="flex items-center gap-3 px-5 py-3.5
                {{ $isToday
                    ? 'bg-violet-50 dark:bg-violet-900/20'
                    : 'bg-gray-50 dark:bg-gray-800/50' }}
                border-b
                {{ $isToday
                    ? 'border-violet-100 dark:border-violet-800/30'
                    : 'border-gray-100 dark:border-gray-800' }}">

                {{-- Day circle --}}
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full
                    {{ $isToday ? 'bg-violet-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                    <span class="text-xs font-bold
                        {{ $isToday ? 'text-white' : 'text-gray-600 dark:text-gray-300' }}">
                        {{ strtoupper(substr($day['label'], 0, 2)) }}
                    </span>
                </div>

                {{-- Day name --}}
                <span class="text-sm font-bold
                    {{ $isToday ? 'text-violet-700 dark:text-violet-300' : 'text-gray-700 dark:text-gray-300' }}">
                    {{ $day['label'] }}
                </span>

                {{-- Today badge --}}
                @if ($isToday)
                    <span class="rounded-full bg-violet-600 px-2.5 py-0.5 text-xs font-semibold text-white">
                        Today
                    </span>
                @endif

                {{-- Slot count pill --}}
                <span class="ml-auto rounded-full px-2 py-0.5 text-xs font-medium
                    {{ $isToday
                        ? 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300'
                        : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                    {{ count($day['slots']) }}
                    {{ count($day['slots']) === 1 ? 'period' : 'periods' }}
                </span>

            </div>

            {{-- ---------------------------------------------------------------- --}}
            {{-- SLOTS LIST                                                        --}}
            {{-- ---------------------------------------------------------------- --}}
            @if (! $hasSlots)
                {{-- Per-day empty state --}}
                <div class="flex items-center gap-3 px-5 py-4">
                    <x-heroicon-o-minus-circle class="h-4 w-4 flex-shrink-0 text-gray-300 dark:text-gray-600" />
                    <p class="text-sm text-gray-400 dark:text-gray-500">No classes scheduled</p>
                </div>

            @else
                <div class="divide-y divide-gray-50 dark:divide-gray-800/60">
                    @foreach ($day['slots'] as $slot)

                        {{-- Detect if this slot is running right now --}}
                        @php
                            $isNow = false;
                            if ($isToday && $slot['start_time'] && $slot['end_time']) {
                                $now   = \Carbon\Carbon::now();
                                $start = \Carbon\Carbon::parse($slot['start_time'])->setDateFrom($now);
                                $end   = \Carbon\Carbon::parse($slot['end_time'])->setDateFrom($now);
                                $isNow = $now->between($start, $end);
                            }
                        @endphp

                        <div class="flex items-start gap-4 px-5 py-4
                            {{ $isNow ? 'bg-violet-50 dark:bg-violet-900/10' : '' }}">

                            {{-- Period number badge --}}
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full
                                {{ $isNow
                                    ? 'bg-violet-600 shadow-md shadow-violet-200 dark:shadow-violet-900'
                                    : 'bg-gray-100 dark:bg-gray-800' }}">
                                <span class="text-xs font-bold
                                    {{ $isNow ? 'text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                    {{ $slot['period'] }}
                                </span>
                            </div>

                            {{-- Main content --}}
                            <div class="min-w-0 flex-1">

                                {{-- Subject name + "Now" badge --}}
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        {{ $slot['subject'] }}
                                    </span>

                                    @if ($isNow)
                                        <span class="rounded-full bg-violet-600 px-2 py-0.5 text-xs font-semibold text-white">
                                            Now
                                        </span>
                                    @endif

                                    {{-- Subject type badge --}}
                                    @if ($slot['subject_label'])
                                        <span class="rounded-md px-1.5 py-0.5 text-xs font-medium {{ $slot['subject_badge'] }}">
                                            {{ $slot['subject_label'] }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Faculty + room --}}
                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    @if ($slot['faculty'])
                                        <span class="flex items-center gap-1">
                                            <x-heroicon-m-user class="h-3 w-3" />
                                            {{ $slot['faculty'] }}
                                        </span>
                                    @endif
                                    @if ($slot['room'])
                                        <span class="flex items-center gap-1">
                                            <x-heroicon-m-map-pin class="h-3 w-3" />
                                            {{ $slot['room'] }}
                                        </span>
                                    @endif
                                </div>

                            </div>

                            {{-- Time --}}
                            @if ($slot['start_time'])
                                <div class="flex-shrink-0 text-right">
                                    <p class="text-xs font-semibold
                                        {{ $isNow ? 'text-violet-700 dark:text-violet-300' : 'text-gray-700 dark:text-gray-300' }}">
                                        {{ $slot['start_time'] }}
                                    </p>
                                    @if ($slot['end_time'])
                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $slot['end_time'] }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    @endforeach
</div>

{{-- ========================================================================== --}}
{{-- LEGEND                                                                      --}}
{{-- ========================================================================== --}}
<div class="rounded-2xl bg-white px-5 py-4 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
    <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Subject Types</p>
    <div class="flex flex-wrap gap-2">
        <span class="rounded-md px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Theory</span>
        <span class="rounded-md px-2 py-1 text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">Practical</span>
        <span class="rounded-md px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">Elective</span>
        <span class="rounded-md px-2 py-1 text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">Project</span>
    </div>
</div>

@endif
{{-- end hasSlots --}}

@endif
{{-- end hasProfile --}}

</x-filament-panels::page>

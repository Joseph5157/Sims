<x-filament-panels::page>
    <div class="space-y-6">

        @if (empty($timetable))
            <x-filament::section>
                <div class="flex items-center gap-3 py-4 text-warning-600 dark:text-warning-400">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 flex-shrink-0" />
                    <p class="text-sm">No timetable slots assigned. Contact the administrator to set up your schedule.</p>
                </div>
            </x-filament::section>
        @else
            {{-- Legend --}}
            <div class="flex flex-wrap gap-3 text-xs font-medium text-gray-500 dark:text-gray-400">
                @php
                    $typeBadges = [
                        'theory'     => ['label' => 'Theory',     'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
                        'practical'  => ['label' => 'Practical',  'class' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
                        'elective'   => ['label' => 'Elective',   'class' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'],
                        'project'    => ['label' => 'Project',    'class' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
                    ];
                @endphp
                @foreach ($typeBadges as $type => $meta)
                    <span class="inline-flex items-center gap-1.5">
                        <span class="inline-block rounded px-1.5 py-0.5 text-xs font-semibold {{ $meta['class'] }}">
                            {{ $meta['label'] }}
                        </span>
                    </span>
                @endforeach
            </div>

            {{-- Day sections --}}
            @foreach ($timetable as $day)
                @php $isToday = ($day['value'] === $todayValue); @endphp

                <div class="rounded-xl border {{ $isToday ? 'border-primary-400 dark:border-primary-600 shadow-md' : 'border-gray-200 dark:border-gray-700' }} overflow-hidden">

                    {{-- Day header --}}
                    <div class="flex items-center justify-between px-5 py-3
                        {{ $isToday
                            ? 'bg-primary-600 dark:bg-primary-700'
                            : 'bg-gray-100 dark:bg-gray-800' }}">
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-bold {{ $isToday ? 'text-white' : 'text-gray-800 dark:text-gray-100' }}">
                                {{ $day['label'] }}
                            </h3>
                            @if ($isToday)
                                <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs font-semibold text-white">
                                    Today
                                </span>
                            @endif
                        </div>
                        <span class="text-xs {{ $isToday ? 'text-primary-100' : 'text-gray-500 dark:text-gray-400' }}">
                            {{ $day['slots']->count() }} {{ Str::plural('period', $day['slots']->count()) }}
                        </span>
                    </div>

                    {{-- Slots --}}
                    <div class="divide-y divide-gray-100 dark:divide-gray-700/60 bg-white dark:bg-gray-900">
                        @forelse ($day['slots'] as $slot)
                            @php
                                $subjectType = $slot->subject?->subject_type?->value ?? null;
                                $typeMeta = match($subjectType) {
                                    'theory'    => ['label' => 'Theory',    'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
                                    'practical' => ['label' => 'Practical', 'class' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
                                    'elective'  => ['label' => 'Elective',  'class' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'],
                                    'project'   => ['label' => 'Project',   'class' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
                                    default     => ['label' => null,        'class' => ''],
                                };

                                $periodNum  = $slot->period_number ?? $slot->period ?? '—';
                                $startTime  = $slot->start_time ? \Carbon\Carbon::parse($slot->start_time)->format('h:i A') : null;
                                $endTime    = $slot->end_time   ? \Carbon\Carbon::parse($slot->end_time)->format('h:i A')   : null;
                            @endphp

                            <div class="flex items-center gap-4 px-5 py-3.5 {{ $isToday ? 'hover:bg-primary-50/40 dark:hover:bg-primary-900/10' : 'hover:bg-gray-50 dark:hover:bg-gray-800/40' }} transition-colors">

                                {{-- Period badge --}}
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg
                                    {{ $isToday ? 'bg-primary-100 dark:bg-primary-900/30' : 'bg-gray-100 dark:bg-gray-800' }}">
                                    <span class="text-sm font-bold {{ $isToday ? 'text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-300' }}">
                                        {{ $periodNum }}
                                    </span>
                                </div>

                                {{-- Time --}}
                                <div class="w-32 flex-shrink-0 text-xs text-gray-500 dark:text-gray-400">
                                    @if ($startTime && $endTime)
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $startTime }}</span>
                                        <span class="mx-0.5">–</span>
                                        <span>{{ $endTime }}</span>
                                    @else
                                        <span class="italic">No time set</span>
                                    @endif
                                </div>

                                {{-- Subject + type badge --}}
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                            {{ $slot->subject?->name ?? '—' }}
                                        </span>
                                        @if ($typeMeta['label'])
                                            <span class="inline-block rounded px-1.5 py-0.5 text-xs font-semibold {{ $typeMeta['class'] }}">
                                                {{ $typeMeta['label'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $slot->subject?->code ?? '' }}
                                    </div>
                                </div>

                                {{-- Class --}}
                                <div class="flex-shrink-0 text-right">
                                    <span class="inline-block rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                        {{ $slot->collegeClass?->name ?? '—' }}
                                    </span>
                                </div>

                            </div>
                        @empty
                            <p class="py-4 text-center text-sm text-gray-400 dark:text-gray-500">No periods.</p>
                        @endforelse
                    </div>

                </div>
            @endforeach
        @endif

    </div>
</x-filament-panels::page>

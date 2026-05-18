<x-filament-panels::page>
    <div class="space-y-4">
        <x-filament::section>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-400">Class</label>
                    <select
                        wire:model.live="selectedClassId"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    >
                        <option value="">— Select class —</option>
                        @foreach ($facultyClasses as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-400">Month</label>
                    <select
                        wire:model.live="month"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    >
                        @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $i => $monthName)
                            <option value="{{ $i + 1 }}" @selected($month == $i + 1)>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-400">Year</label>
                    <select
                        wire:model.live="year"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    >
                        @foreach ([now()->year - 1, now()->year, now()->year + 1] as $calendarYear)
                            <option value="{{ $calendarYear }}" @selected($year == $calendarYear)>{{ $calendarYear }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-filament::section>

        <div class="flex flex-wrap items-center gap-4 text-xs font-medium">
            @foreach ([
                ['label' => 'Present', 'icon' => 'heroicon-o-check-circle', 'bg' => 'bg-green-500'],
                ['label' => 'Absent', 'icon' => 'heroicon-o-x-circle', 'bg' => 'bg-red-500'],
                ['label' => 'Late', 'icon' => 'heroicon-o-clock', 'bg' => 'bg-yellow-500'],
                ['label' => 'Excused', 'icon' => 'heroicon-o-shield-check', 'bg' => 'bg-blue-500'],
            ] as $item)
                <span class="flex items-center gap-1.5">
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded {{ $item['bg'] }} text-white">
                        <x-dynamic-component :component="$item['icon']" class="h-3 w-3" />
                    </span>
                    {{ $item['label'] }}
                </span>
            @endforeach
            <span class="flex items-center gap-1.5 text-gray-400">
                <span class="inline-flex h-5 w-5 items-center justify-center rounded bg-gray-200 text-gray-500 dark:bg-gray-600 dark:text-gray-200">
                    <x-heroicon-o-minus-circle class="h-3 w-3" />
                </span>
                Not marked
            </span>
            <span class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400">
                <span class="inline-flex h-5 w-5 items-center justify-center rounded border border-amber-300 bg-amber-50 text-[10px] font-bold leading-5 dark:border-amber-600 dark:bg-amber-900/30">D</span>
                Draft change
            </span>
            <span class="ml-2 hidden text-gray-400 sm:block">Select a cell, choose a status, then save all changes together.</span>
        </div>

        @if ($selectedClassId && count($students) > 0)
            @php
                $activeStudent = $this->getActiveStudent();
                $activeStatus = $this->getActiveCellStatus();
                $activeDateLabel = $this->getActiveCellDateLabel();
            @endphp

            <x-filament::section>
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $facultyClasses[$selectedClassId] ?? '' }}</h3>
                            <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                {{ $this->getMonthName() }}
                            </span>
                            @if ($hasUnsavedChanges)
                                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                    Unsaved changes
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ count($students) }} students. Changes stay in draft until you click Save all.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            wire:click="discardChanges"
                            @disabled(! $hasUnsavedChanges)
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800"
                        >
                            <x-heroicon-o-arrow-uturn-left class="h-4 w-4" />
                            Discard changes
                        </button>
                        <button
                            type="button"
                            wire:click="saveAll"
                            @disabled(! $hasUnsavedChanges)
                            class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <x-heroicon-o-check class="h-4 w-4" />
                            Save all
                        </button>
                    </div>
                </div>

                <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50" aria-live="polite">
                    @if ($activeStudent && $activeDateLabel)
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Selected cell</p>
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $activeStudent['name'] }}
                                    <span class="text-gray-400 dark:text-gray-500">•</span>
                                    {{ $activeDateLabel }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Current draft status:
                                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ $this->getStatusLabel($activeStatus) }}</span>
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
                                @foreach ([
                                    'present' => ['label' => 'Present', 'icon' => 'heroicon-o-check-circle', 'class' => 'bg-green-600 hover:bg-green-700'],
                                    'absent' => ['label' => 'Absent', 'icon' => 'heroicon-o-x-circle', 'class' => 'bg-red-600 hover:bg-red-700'],
                                    'late' => ['label' => 'Late', 'icon' => 'heroicon-o-clock', 'class' => 'bg-yellow-500 hover:bg-yellow-600'],
                                    'excused' => ['label' => 'Excused', 'icon' => 'heroicon-o-shield-check', 'class' => 'bg-blue-600 hover:bg-blue-700'],
                                ] as $statusValue => $meta)
                                    <button
                                        type="button"
                                        wire:click="applyStatus('{{ $statusValue }}')"
                                        aria-pressed="{{ $activeStatus === $statusValue ? 'true' : 'false' }}"
                                        class="inline-flex min-h-[40px] items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold text-white transition {{ $meta['class'] }} {{ $activeStatus === $statusValue ? 'ring-2 ring-offset-2 ring-gray-900/20 dark:ring-white/40 dark:ring-offset-gray-900' : '' }}"
                                    >
                                        <x-dynamic-component :component="$meta['icon']" class="h-4 w-4" />
                                        <span>{{ $meta['label'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col gap-1 text-sm text-gray-500 dark:text-gray-400">
                            <p class="font-medium text-gray-700 dark:text-gray-200">Choose a cell to edit.</p>
                            <p>Select a student-day cell, assign one of the four attendance states, then save all changes together.</p>
                        </div>
                    @endif
                </div>
            </x-filament::section>

            <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm dark:border-gray-700">
                <div class="overflow-x-auto" style="max-height: 72vh; overflow-y: auto;">
                    <table class="w-full border-collapse text-xs" style="min-width: max-content;">
                        <thead class="sticky top-0 z-20">
                            <tr class="bg-gray-100 dark:bg-gray-800">
                                <th scope="col" class="sticky left-0 z-30 min-w-[180px] whitespace-nowrap border-b border-r border-gray-200 bg-gray-100 px-3 py-2.5 text-left font-semibold text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                    Student
                                </th>
                                @for ($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $isToday = ($d == now()->day && $month == now()->month && $year == now()->year);
                                    @endphp
                                    <th scope="col" class="w-10 min-w-[36px] border-b border-gray-200 py-2.5 text-center font-semibold dark:border-gray-700 {{ $isToday ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300' : 'text-gray-600 dark:text-gray-300' }}">
                                        {{ $d }}
                                    </th>
                                @endfor
                                <th scope="col" class="whitespace-nowrap border-b border-l border-gray-200 px-3 py-2.5 text-center font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-200">
                                    %
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr class="bg-slate-50 dark:bg-slate-900/40">
                                <td class="sticky left-0 z-10 whitespace-nowrap border-r border-gray-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:border-gray-700 dark:bg-slate-900/40 dark:text-slate-400">
                                    Attendance / Day
                                </td>
                                @for ($d = 1; $d <= $daysInMonth; $d++)
                                    @php $isToday = ($d == now()->day && $month == now()->month && $year == now()->year); @endphp
                                    <td class="px-0.5 py-1.5 text-center font-semibold text-slate-600 dark:text-slate-400 {{ $isToday ? 'bg-primary-50 dark:bg-primary-900/10' : '' }}">
                                        {{ $dayTotals[$d] > 0 ? $dayTotals[$d] : '' }}
                                    </td>
                                @endfor
                                <td class="border-l border-gray-200 dark:border-gray-700"></td>
                            </tr>

                            @foreach ($students as $student)
                                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/40 {{ $loop->even ? 'bg-gray-50/50 dark:bg-gray-800/20' : '' }}">
                                    <th scope="row" class="sticky left-0 z-10 whitespace-nowrap border-r border-gray-200 px-3 py-2 text-left dark:border-gray-700 {{ $loop->even ? 'bg-gray-50 dark:bg-gray-800/50' : 'bg-white dark:bg-gray-900' }}">
                                        <div class="text-xs font-semibold text-gray-900 dark:text-gray-100">{{ $student['name'] }}</div>
                                        <div class="font-mono text-xs text-gray-400 dark:text-gray-500">{{ $student['roll_number'] }}</div>
                                    </th>

                                    @for ($d = 1; $d <= $daysInMonth; $d++)
                                        @php
                                            $cellStatus = $this->getDisplayStatus($student['id'], $d);
                                            $isToday = ($d == now()->day && $month == now()->month && $year == now()->year);
                                            $isSelected = $activeStudentId === $student['id'] && $activeDay === $d;
                                            $isDraft = $this->isDraftCell($student['id'], $d);
                                            $btnColor = match ($cellStatus) {
                                                'present' => 'bg-green-500 text-white hover:bg-green-600',
                                                'absent' => 'bg-red-500 text-white hover:bg-red-600',
                                                'late' => 'bg-yellow-500 text-white hover:bg-yellow-600',
                                                'excused' => 'bg-blue-500 text-white hover:bg-blue-600',
                                                default => 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600',
                                            };
                                        @endphp
                                        <td class="p-0.5 text-center {{ $isToday ? 'bg-primary-50 dark:bg-primary-900/10' : '' }}">
                                            <button
                                                type="button"
                                                wire:click="selectCell({{ $student['id'] }}, {{ $d }})"
                                                aria-label="{{ $this->getCellAriaLabel($student, $d, $cellStatus, $isSelected, $isDraft) }}"
                                                aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                                class="relative flex h-8 w-8 items-center justify-center rounded text-xs font-bold transition-all active:scale-95 focus:outline-none {{ $btnColor }} {{ $isSelected ? 'ring-2 ring-primary-500 ring-offset-1 dark:ring-primary-400 dark:ring-offset-gray-900' : '' }} {{ $isDraft ? 'shadow-md shadow-amber-500/20' : '' }}"
                                            >
                                                @php
                                                    $icon = $this->getStatusIcon($cellStatus);
                                                @endphp
                                                <x-dynamic-component :component="$icon" class="h-4 w-4" aria-hidden="true" />
                                                <span class="sr-only">{{ $this->getStatusLabel($cellStatus) }}</span>
                                                @if ($isDraft)
                                                    <span class="absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full border border-white bg-amber-400 dark:border-gray-900"></span>
                                                @endif
                                            </button>
                                        </td>
                                    @endfor

                                    <td class="whitespace-nowrap border-l border-gray-200 px-3 py-2 text-center dark:border-gray-700">
                                        <span class="inline-block rounded-full px-2 py-0.5 text-xs font-bold
                                            {{ $student['percentage'] >= 75
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                                : ($student['percentage'] >= 60
                                                    ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                                            {{ $student['percentage'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif ($selectedClassId)
            <x-filament::section>
                <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">No students found in this class.</p>
            </x-filament::section>
        @else
            <x-filament::section>
                <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">Select a class to view the attendance grid.</p>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>

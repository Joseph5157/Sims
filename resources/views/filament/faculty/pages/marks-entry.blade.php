<x-filament-panels::page>
    <div class="space-y-4">

        {{-- ------------------------------------------------------------------ --}}
        {{-- Filter bar                                                          --}}
        {{-- ------------------------------------------------------------------ --}}
        <x-filament::section>
            <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">

                {{-- Exam Group --}}
                <div class="flex-1 min-w-[220px]">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Exam Group
                    </label>
                    <select
                        wire:model.live="examGroupId"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    >
                        <option value="">— Select exam group —</option>
                        @foreach ($availableExamGroups as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @if ($examGroupId && empty($availableExamGroups))
                        <p class="mt-1 text-xs text-warning-600 dark:text-warning-400">No FA/SA exam groups found for your classes.</p>
                    @endif
                </div>

                {{-- Subject --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Subject
                    </label>
                    <select
                        wire:model.live="subjectId"
                        @disabled(!$examGroupId)
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    >
                        <option value="">— Select subject —</option>
                        @foreach ($availableSubjects as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @if ($examGroupId && $subjectId === null && empty($availableSubjects))
                        <p class="mt-1 text-xs text-warning-600 dark:text-warning-400">No exams set up for this group. Ask admin to create exams first.</p>
                    @endif
                </div>

                {{-- Mode badge --}}
                @if ($examGroupType)
                    @php
                        $modeLabel = $examGroupType === 'fa' ? 'Formative (FA)' : 'Summative (SA)';
                        $modeBg    = $examGroupType === 'fa' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300';
                    @endphp
                    <div class="flex-shrink-0 pb-0.5">
                        <span class="inline-block rounded-lg px-3 py-2 text-xs font-bold {{ $modeBg }}">
                            {{ $modeLabel }}
                        </span>
                    </div>
                @endif

            </div>
        </x-filament::section>

        {{-- ------------------------------------------------------------------ --}}
        {{-- No exams configured                                                 --}}
        {{-- ------------------------------------------------------------------ --}}
        @if ($examGroupId && $subjectId && empty($exams))
            <x-filament::section>
                <div class="flex items-center gap-3 py-4 text-warning-600 dark:text-warning-400">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 flex-shrink-0" />
                    <p class="text-sm">
                        No exam records found for this group + subject.
                        Ask the admin to create the exam records first.
                    </p>
                </div>
            </x-filament::section>
        @endif

        {{-- ------------------------------------------------------------------ --}}
        {{-- Marks table                                                         --}}
        {{-- ------------------------------------------------------------------ --}}
        @if (!empty($students) && !empty($exams))

            @php
                $isFa      = ($examGroupType === 'fa');
                $maxTotal  = array_sum(array_column($exams, 'maximum_marks'));
                $gradeColors = [
                    'A1' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                    'A2' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                    'B1' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                    'B2' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                    'C1' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                    'C2' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                    'D1' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
                    'D2' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
                    'E'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                    'AB' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
                ];
            @endphp

            <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" style="min-width: max-content;">

                        {{-- ------------------------------------------------ --}}
                        {{-- Header                                             --}}
                        {{-- ------------------------------------------------ --}}
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/60">

                                {{-- Fixed columns --}}
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">
                                    Roll
                                </th>
                                <th class="min-w-[180px] px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">
                                    Student
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">
                                    Absent
                                </th>

                                {{-- Tool columns (FA: Tool 1/2/3/4, SA: Marks) --}}
                                @foreach ($exams as $exam)
                                    <th class="min-w-[90px] px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">
                                        {{ $exam['label'] }}
                                        <div class="mt-0.5 text-xs font-normal normal-case text-gray-400 dark:text-gray-500">
                                            / {{ (int) $exam['maximum_marks'] }}
                                        </div>
                                    </th>
                                @endforeach

                                {{-- FA only: Total --}}
                                @if ($isFa)
                                    <th class="min-w-[80px] px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">
                                        Total
                                        <div class="mt-0.5 text-xs font-normal normal-case text-gray-400 dark:text-gray-500">
                                            / {{ (int) $maxTotal }}
                                        </div>
                                    </th>
                                @endif

                                {{-- Grade --}}
                                <th class="min-w-[70px] px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">
                                    Grade
                                </th>

                                {{-- Writing Language --}}
                                <th class="min-w-[140px] px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">
                                    Writing Lang.
                                </th>

                            </tr>
                        </thead>

                        {{-- ------------------------------------------------ --}}
                        {{-- Body                                               --}}
                        {{-- ------------------------------------------------ --}}
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @foreach ($students as $student)
                                @php
                                    $sid      = $student['id'];
                                    $isAbs    = (bool) ($absent[$sid] ?? false);
                                    $grade    = $grades[$sid] ?? '—';
                                    $total    = $totals[$sid] ?? 0;
                                    $gradeClass = $gradeColors[$grade] ?? 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400';
                                @endphp

                                <tr class="transition-colors {{ $loop->even ? 'bg-gray-50/50 dark:bg-gray-800/20' : '' }} {{ $isAbs ? 'opacity-60' : '' }}">

                                    {{-- Roll number --}}
                                    <td class="whitespace-nowrap px-4 py-2.5">
                                        <span class="font-mono text-xs text-gray-500 dark:text-gray-400">
                                            {{ $student['roll_number'] }}
                                        </span>
                                    </td>

                                    {{-- Student name --}}
                                    <td class="px-4 py-2.5">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $student['name'] }}
                                        </span>
                                    </td>

                                    {{-- Absent toggle --}}
                                    <td class="px-4 py-2.5 text-center">
                                        <label class="relative inline-flex cursor-pointer items-center">
                                            <input
                                                type="checkbox"
                                                wire:model.live="absent.{{ $sid }}"
                                                class="peer sr-only"
                                            />
                                            <div class="peer h-5 w-9 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-red-500 peer-checked:after:translate-x-full peer-focus:outline-none dark:bg-gray-600"></div>
                                        </label>
                                    </td>

                                    {{-- Tool / Marks inputs --}}
                                    @foreach ($exams as $exam)
                                        @php
                                            $eid = $exam['id'];
                                            $max = $exam['maximum_marks'];
                                        @endphp
                                        <td class="px-2 py-2">
                                            <input
                                                type="number"
                                                wire:model.live.debounce.400ms="marks.{{ $sid }}.{{ $eid }}"
                                                min="0"
                                                max="{{ $max }}"
                                                step="0.5"
                                                placeholder="—"
                                                @disabled($isAbs)
                                                class="w-full rounded-lg border px-2 py-1.5 text-center text-sm font-medium shadow-sm transition-colors
                                                    @if($isAbs)
                                                        border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-600
                                                    @else
                                                        border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100
                                                    @endif"
                                            />
                                        </td>
                                    @endforeach

                                    {{-- FA total (read only) --}}
                                    @if ($isFa)
                                        <td class="px-2 py-2 text-center">
                                            @if ($isAbs)
                                                <span class="text-xs text-gray-400">—</span>
                                            @else
                                                <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                    {{ number_format($total, $total == floor($total) ? 0 : 1) }}
                                                </span>
                                            @endif
                                        </td>
                                    @endif

                                    {{-- Grade badge (read only) --}}
                                    <td class="px-2 py-2 text-center">
                                        <span class="inline-block min-w-[36px] rounded px-2 py-0.5 text-xs font-bold {{ $gradeClass }}">
                                            {{ $grade }}
                                        </span>
                                    </td>

                                    {{-- Writing Language --}}
                                    <td class="px-2 py-2">
                                        <input
                                            type="text"
                                            wire:model.lazy="writingLanguage.{{ $sid }}"
                                            placeholder="e.g. English"
                                            maxlength="100"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-700 shadow-sm placeholder-gray-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:placeholder-gray-600"
                                        />
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>

                        {{-- ------------------------------------------------ --}}
                        {{-- Footer summary row                                 --}}
                        {{-- ------------------------------------------------ --}}
                        <tfoot>
                            @php
                                $presentCount = count(array_filter($absent, fn($v) => !$v));
                                $absentCount  = count(array_filter($absent, fn($v) => (bool)$v));
                            @endphp
                            <tr class="border-t-2 border-gray-200 bg-gray-100 dark:border-gray-600 dark:bg-gray-800/80">
                                <td colspan="{{ 3 + count($exams) + ($isFa ? 1 : 0) + 2 }}"
                                    class="px-4 py-2.5 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold text-green-700 dark:text-green-400">{{ $presentCount }} present</span>
                                    &bull;
                                    <span class="font-semibold text-red-700 dark:text-red-400">{{ $absentCount }} absent</span>
                                    &bull;
                                    {{ count($students) }} total students
                                </td>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>

            {{-- ---------------------------------------------------------------- --}}
            {{-- Sticky Save button                                               --}}
            {{-- ---------------------------------------------------------------- --}}
            <div class="sticky bottom-4 z-10">
                <button
                    wire:click="save"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-75 cursor-not-allowed"
                    class="w-full rounded-xl bg-primary-600 px-6 py-4 text-base font-bold text-white shadow-lg transition-colors hover:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-400/50"
                >
                    <span wire:loading.remove wire:target="save">
                        Save Marks
                    </span>
                    <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Saving…
                    </span>
                </button>
            </div>

        @elseif ($examGroupId && $subjectId && !empty($availableSubjects))
            <x-filament::section>
                <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    No students found in this class.
                </p>
            </x-filament::section>

        @elseif (!$examGroupId)
            <x-filament::section>
                <div class="flex flex-col items-center gap-3 py-10 text-gray-400 dark:text-gray-500">
                    <x-heroicon-o-pencil-square class="h-10 w-10" />
                    <p class="text-sm">Select an exam group and subject to begin entering marks.</p>
                </div>
            </x-filament::section>
        @endif

    </div>
</x-filament-panels::page>

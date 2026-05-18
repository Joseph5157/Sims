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
                <x-heroicon-o-document-chart-bar class="h-6 w-6 text-white" />
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">My Results</h1>
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
{{-- NO RESULTS STATE                                                            --}}
{{-- ========================================================================== --}}
@if (! $hasResults)
    <div class="flex flex-col items-center justify-center gap-4 rounded-2xl bg-white py-20 text-center shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
            <x-heroicon-o-document-chart-bar class="h-8 w-8 text-gray-400 dark:text-gray-500" />
        </div>
        <div>
            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">No results published yet</p>
            <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">
                Check back after your exams are published by admin.
            </p>
        </div>
    </div>

@else

{{-- ========================================================================== --}}
{{-- EXAM GROUP CARDS                                                            --}}
{{-- ========================================================================== --}}
<div class="space-y-3">
@foreach ($examGroups as $group)
    @php $isOpen = in_array($group['id'], $openGroups, true); @endphp

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">

        {{-- ------------------------------------------------------------------ --}}
        {{-- CARD HEADER (clickable to expand/collapse)                         --}}
        {{-- ------------------------------------------------------------------ --}}
        <button
            wire:click="toggleGroup({{ $group['id'] }})"
            class="flex w-full items-center gap-3 px-5 py-4 text-left transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/40"
        >
            {{-- Exam icon --}}
            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl
                {{ $group['type'] === 'fa' ? 'bg-blue-100 dark:bg-blue-900/30' : ($group['type'] === 'sa' ? 'bg-amber-100 dark:bg-amber-900/30' : 'bg-violet-100 dark:bg-violet-900/30') }}">
                <x-heroicon-o-clipboard-document-list class="h-5 w-5
                    {{ $group['type'] === 'fa' ? 'text-blue-600 dark:text-blue-400' : ($group['type'] === 'sa' ? 'text-amber-600 dark:text-amber-400' : 'text-violet-600 dark:text-violet-400') }}" />
            </div>

            {{-- Name + date --}}
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                        {{ $group['name'] }}
                    </span>

                    {{-- Type badge: FA = blue, SA = amber --}}
                    @if ($group['type_label'])
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold
                            {{ $group['type'] === 'fa'
                                ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
                                : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' }}">
                            {{ $group['type_label'] }}
                        </span>
                    @endif

                    @if ($group['conducted_date'] !== '—')
                        <span class="text-xs text-gray-400 dark:text-gray-500">
                            {{ $group['conducted_date'] }}
                        </span>
                    @endif
                </div>

                {{-- Marks summary on mobile (below name) --}}
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                    {{ $group['obtained'] }} / {{ $group['maximum'] }} marks
                </p>
            </div>

            {{-- Right side: percentage + grade badge + chevron --}}
            <div class="flex flex-shrink-0 items-center gap-3">
                @if ($group['maximum'] > 0)
                    <span class="hidden text-sm font-bold text-gray-700 dark:text-gray-300 sm:block">
                        {{ $group['percentage'] }}%
                    </span>
                    <span class="rounded-lg px-2.5 py-1 text-xs font-bold {{ $group['grade_class'] }}">
                        {{ $group['grade'] }}
                    </span>
                @else
                    <span class="rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                        No scores
                    </span>
                @endif

                <x-heroicon-o-chevron-down class="h-4 w-4 text-gray-400 transition-transform duration-200 {{ $isOpen ? 'rotate-180' : '' }}" />
            </div>
        </button>

        {{-- ------------------------------------------------------------------ --}}
        {{-- CARD BODY (collapsible)                                             --}}
        {{-- ------------------------------------------------------------------ --}}
        @if ($isOpen)
            <div class="border-t border-gray-100 dark:border-gray-800">

                @if (empty($group['subjects']))
                    <div class="flex flex-col items-center justify-center gap-2 py-8 text-center">
                        <x-heroicon-o-document-text class="h-6 w-6 text-gray-300 dark:text-gray-600" />
                        <p class="text-sm text-gray-400 dark:text-gray-500">No subject data available.</p>
                    </div>

                @else
                    <div class="overflow-x-auto">

                        {{-- ================================================ --}}
                        {{-- FA TABLE: Subject | Tool 1..N | Total | Max | Grade --}}
                        {{-- ================================================ --}}
                        @if ($group['type'] === 'fa')
                            <table class="w-full min-w-max text-sm">
                                <thead>
                                    <tr class="bg-blue-50 dark:bg-blue-900/10">
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-400">
                                            Subject
                                        </th>
                                        @for ($t = 0; $t < $group['max_tools']; $t++)
                                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-400">
                                                Tool {{ $t + 1 }}
                                            </th>
                                        @endfor
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-400">Total</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-400">Max</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-400">Grade</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                                    @foreach ($group['subjects'] as $subject)
                                        <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                            <td class="px-5 py-3.5 font-medium text-gray-900 dark:text-gray-100">
                                                {{ $subject['subject'] }}
                                            </td>
                                            @foreach ($subject['tools'] as $tool)
                                                <td class="px-4 py-3.5 text-center">
                                                    @if ($tool['absent'])
                                                        <span class="rounded bg-red-100 px-1.5 py-0.5 text-xs font-semibold text-red-600 dark:bg-red-900/30 dark:text-red-400">AB</span>
                                                    @elseif ($tool['marks'] !== null)
                                                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $tool['marks'] }}</span>
                                                        @if ($tool['max'] > 0)
                                                            <span class="text-xs text-gray-400 dark:text-gray-500">/{{ $tool['max'] }}</span>
                                                        @endif
                                                    @else
                                                        <span class="text-gray-300 dark:text-gray-600">—</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="px-4 py-3.5 text-center font-bold text-gray-800 dark:text-gray-200">
                                                {{ $subject['obtained'] }}
                                            </td>
                                            <td class="px-4 py-3.5 text-center text-gray-500 dark:text-gray-400">
                                                {{ $subject['maximum'] }}
                                            </td>
                                            <td class="px-4 py-3.5 text-center">
                                                <span class="rounded-lg px-2 py-0.5 text-xs font-bold {{ $subject['grade_class'] }}">
                                                    {{ $subject['grade'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                {{-- Totals footer --}}
                                <tfoot>
                                    <tr class="border-t border-blue-100 bg-blue-50/50 dark:border-blue-900/30 dark:bg-blue-900/10">
                                        <td class="px-5 py-3 text-xs font-semibold uppercase text-blue-700 dark:text-blue-400" colspan="{{ $group['max_tools'] + 1 }}">
                                            Overall
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-blue-700 dark:text-blue-400">
                                            {{ $group['obtained'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-blue-700 dark:text-blue-400">
                                            {{ $group['maximum'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="rounded-lg px-2 py-0.5 text-xs font-bold {{ $group['grade_class'] }}">
                                                {{ $group['grade'] }}
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>

                        {{-- ================================================ --}}
                        {{-- SA TABLE: Subject | Marks | Max Marks | Grade     --}}
                        {{-- ================================================ --}}
                        @else
                            <table class="w-full min-w-[400px] text-sm">
                                <thead>
                                    <tr class="bg-amber-50 dark:bg-amber-900/10">
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-400">
                                            Subject
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-400">Marks</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-400">Max Marks</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-400">%</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-400">Grade</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                                    @foreach ($group['subjects'] as $subject)
                                        @php
                                            $tool = $subject['tools'][0] ?? null;
                                            $subPct = $subject['maximum'] > 0
                                                ? round(($subject['obtained'] / $subject['maximum']) * 100, 1)
                                                : 0.0;
                                        @endphp
                                        <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                            <td class="px-5 py-3.5 font-medium text-gray-900 dark:text-gray-100">
                                                {{ $subject['subject'] }}
                                            </td>
                                            <td class="px-4 py-3.5 text-center">
                                                @if ($tool && $tool['absent'])
                                                    <span class="rounded bg-red-100 px-1.5 py-0.5 text-xs font-semibold text-red-600 dark:bg-red-900/30 dark:text-red-400">AB</span>
                                                @elseif ($subject['obtained'] > 0 || ($tool && $tool['marks'] !== null))
                                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $subject['obtained'] }}</span>
                                                @else
                                                    <span class="text-gray-300 dark:text-gray-600">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3.5 text-center text-gray-500 dark:text-gray-400">
                                                {{ $subject['maximum'] }}
                                            </td>
                                            <td class="px-4 py-3.5 text-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ $subPct }}%
                                            </td>
                                            <td class="px-4 py-3.5 text-center">
                                                <span class="rounded-lg px-2 py-0.5 text-xs font-bold {{ $subject['grade_class'] }}">
                                                    {{ $subject['grade'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                {{-- Totals footer --}}
                                <tfoot>
                                    @php
                                        $groupOverallPct = $group['maximum'] > 0
                                            ? round(($group['obtained'] / $group['maximum']) * 100, 1)
                                            : 0.0;
                                    @endphp
                                    <tr class="border-t border-amber-100 bg-amber-50/50 dark:border-amber-900/30 dark:bg-amber-900/10">
                                        <td class="px-5 py-3 text-xs font-semibold uppercase text-amber-700 dark:text-amber-400">Overall</td>
                                        <td class="px-4 py-3 text-center font-bold text-amber-700 dark:text-amber-400">
                                            {{ $group['obtained'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-amber-700 dark:text-amber-400">
                                            {{ $group['maximum'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-amber-700 dark:text-amber-400">
                                            {{ $groupOverallPct }}%
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="rounded-lg px-2 py-0.5 text-xs font-bold {{ $group['grade_class'] }}">
                                                {{ $group['grade'] }}
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        @endif

                    </div>

                    {{-- Percentage bar (inside expanded body) --}}
                    @if ($group['maximum'] > 0)
                        <div class="border-t border-gray-100 px-5 py-3 dark:border-gray-800">
                            <div class="flex items-center gap-3">
                                <span class="flex-shrink-0 text-xs text-gray-400 dark:text-gray-500">Score</span>
                                <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div
                                        class="h-full rounded-full transition-all duration-500
                                            {{ $group['percentage'] >= 75 ? 'bg-green-500' : ($group['percentage'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                        style="width: {{ $group['percentage'] }}%"
                                    ></div>
                                </div>
                                <span class="flex-shrink-0 text-xs font-bold text-gray-600 dark:text-gray-400">
                                    {{ $group['percentage'] }}%
                                </span>
                            </div>
                        </div>
                    @endif
                @endif

            </div>
        @endif

    </div>
@endforeach
</div>

{{-- ========================================================================== --}}
{{-- ANNUAL RESULT CARD                                                          --}}
{{-- Only shown when at least one SA group is published                         --}}
{{-- ========================================================================== --}}
@if (! empty($annualResult) && $annualResult['show'])
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-2 ring-amber-300 dark:bg-gray-900 dark:ring-amber-600/50">

        {{-- Card header --}}
        <div class="flex items-center gap-3 border-b border-amber-100 bg-amber-50 px-5 py-4 dark:border-amber-900/30 dark:bg-amber-900/20">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-200 dark:bg-amber-900/40">
                <x-heroicon-o-trophy class="h-4 w-4 text-amber-700 dark:text-amber-400" />
            </div>
            <h3 class="text-sm font-bold text-amber-800 dark:text-amber-300">Annual Result</h3>
        </div>

        {{-- Score breakdown --}}
        <div class="p-5">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

                {{-- FA Total --}}
                <div class="rounded-xl bg-blue-50 px-4 py-3 dark:bg-blue-900/20">
                    <p class="text-xs font-medium text-blue-600 dark:text-blue-400">FA Total</p>
                    <p class="mt-1 text-xl font-bold text-blue-700 dark:text-blue-300">
                        {{ $annualResult['fa_obtained'] }}
                        <span class="text-sm font-normal text-blue-500 dark:text-blue-400">/ {{ $annualResult['fa_max'] }}</span>
                    </p>
                </div>

                {{-- SA Total --}}
                <div class="rounded-xl bg-amber-50 px-4 py-3 dark:bg-amber-900/20">
                    <p class="text-xs font-medium text-amber-600 dark:text-amber-400">SA Total</p>
                    <p class="mt-1 text-xl font-bold text-amber-700 dark:text-amber-300">
                        {{ $annualResult['sa_obtained'] }}
                        <span class="text-sm font-normal text-amber-500 dark:text-amber-400">/ {{ $annualResult['sa_max'] }}</span>
                    </p>
                </div>

                {{-- Grand Total --}}
                <div class="rounded-xl bg-violet-50 px-4 py-3 dark:bg-violet-900/20">
                    <p class="text-xs font-medium text-violet-600 dark:text-violet-400">Grand Total</p>
                    <p class="mt-1 text-xl font-bold text-violet-700 dark:text-violet-300">
                        {{ $annualResult['grand_obtained'] }}
                        <span class="text-sm font-normal text-violet-500 dark:text-violet-400">/ {{ $annualResult['grand_max'] }}</span>
                    </p>
                </div>

            </div>

            {{-- Final percentage + grade --}}
            <div class="mt-4 flex items-center justify-between rounded-xl bg-gray-50 px-5 py-4 dark:bg-gray-800/50">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Final Percentage</p>
                    <p class="text-3xl font-extrabold text-gray-900 dark:text-gray-100">
                        {{ $annualResult['percentage'] }}<span class="text-lg font-bold">%</span>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Annual Grade</p>
                    <span class="mt-1 inline-block rounded-xl px-5 py-2 text-2xl font-extrabold {{ $annualResult['grade_class'] }}">
                        {{ $annualResult['grade'] }}
                    </span>
                </div>
            </div>
        </div>

    </div>
@endif

@endif
{{-- end hasResults --}}

@endif
{{-- end hasProfile --}}

</x-filament-panels::page>

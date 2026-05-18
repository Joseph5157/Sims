<x-filament-panels::page>
    <div class="space-y-4">

        {{-- ------------------------------------------------------------------ --}}
        {{-- Filters                                                             --}}
        {{-- ------------------------------------------------------------------ --}}
        <x-filament::section>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">

                {{-- Search --}}
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-400">Search</label>
                    <div class="relative">
                        <x-heroicon-m-magnifying-glass class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Name or roll number…"
                            class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-9 pr-3 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                        />
                    </div>
                </div>

                {{-- Class filter --}}
                @if (count($facultyClasses) > 1)
                    <div class="min-w-[200px]">
                        <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-400">Class</label>
                        <select
                            wire:model.live="filterClassId"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                        >
                            <option value="">— All classes —</option>
                            @foreach ($facultyClasses as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Count --}}
                <div class="flex-shrink-0 text-sm text-gray-500 dark:text-gray-400 sm:pb-2">
                    {{ count($students) }} student{{ count($students) !== 1 ? 's' : '' }}
                </div>

            </div>
        </x-filament::section>

        {{-- ------------------------------------------------------------------ --}}
        {{-- Student table                                                       --}}
        {{-- ------------------------------------------------------------------ --}}
        @if (count($students) > 0)
            <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm dark:border-gray-700">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/60">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Class</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Attendance</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Last Grade</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @foreach ($students as $student)
                            @php
                                $attPct   = $student['attendance_pct'];
                                $attColor = $attPct === null ? 'text-gray-400'
                                    : ($attPct >= 75 ? 'text-green-700 dark:text-green-400'
                                    : ($attPct >= 60 ? 'text-yellow-700 dark:text-yellow-400'
                                    : 'text-red-700 dark:text-red-400'));
                                $attBg    = $attPct === null ? 'bg-gray-100 dark:bg-gray-700'
                                    : ($attPct >= 75 ? 'bg-green-100 dark:bg-green-900/30'
                                    : ($attPct >= 60 ? 'bg-yellow-100 dark:bg-yellow-900/30'
                                    : 'bg-red-100 dark:bg-red-900/30'));

                                $statusColors = [
                                    'success' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'info'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'warning' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    'danger'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'gray'    => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                ];
                                $statusClass = $statusColors[$student['status_color']] ?? $statusColors['gray'];
                            @endphp
                            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/40">

                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $student['name'] }}</div>
                                    <div class="font-mono text-xs text-gray-400 dark:text-gray-500">{{ $student['roll_number'] }}</div>
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $student['class_name'] }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if ($attPct !== null)
                                        <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-bold {{ $attBg }} {{ $attColor }}">
                                            {{ $attPct }}%
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if ($student['last_grade'])
                                        <span class="inline-block rounded px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                            {{ $student['last_grade'] }}
                                        </span>
                                        @if ($student['last_exam_name'])
                                            <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate max-w-[120px] mx-auto">
                                                {{ $student['last_exam_name'] }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">
                                        {{ $student['status_label'] }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <button
                                        wire:click="openModal({{ $student['id'] }})"
                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-primary-600 hover:bg-primary-50 dark:text-primary-400 dark:hover:bg-primary-900/20 transition-colors"
                                    >
                                        <x-heroicon-m-eye class="h-3.5 w-3.5" />
                                        View
                                    </button>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @elseif (!empty($search) || $filterClassId)
            <x-filament::section>
                <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">No students match your search.</p>
            </x-filament::section>
        @else
            <x-filament::section>
                <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">No students found in your assigned classes.</p>
            </x-filament::section>
        @endif

    </div>

    {{-- -------------------------------------------------------------------- --}}
    {{-- Student detail modal                                                  --}}
    {{-- -------------------------------------------------------------------- --}}
    @if ($selectedStudentId && !empty($modalStudent))
        {{-- Backdrop --}}
        <div
            wire:click="closeModal"
            class="fixed inset-0 z-40 bg-gray-900/50 dark:bg-gray-950/70 backdrop-blur-sm"
        ></div>

        {{-- Modal panel --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div
                class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-700"
                wire:click.stop
            >

                {{-- Modal header --}}
                <div class="sticky top-0 z-10 flex items-center justify-between rounded-t-2xl border-b border-gray-200 bg-white px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $modalStudent['name'] }}</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $modalStudent['class_name'] }} &bull; Roll {{ $modalStudent['roll_number'] }}
                        </p>
                    </div>
                    <button
                        wire:click="closeModal"
                        class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-300 transition-colors"
                    >
                        <x-heroicon-m-x-mark class="h-5 w-5" />
                    </button>
                </div>

                <div class="space-y-5 p-6">

                    {{-- Profile grid --}}
                    <div>
                        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Profile</h3>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach ([
                                ['label' => 'Admission No', 'value' => $modalStudent['admission_number']],
                                ['label' => 'Department',   'value' => $modalStudent['department']],
                                ['label' => 'Gender',       'value' => $modalStudent['gender']],
                                ['label' => 'Blood Group',  'value' => $modalStudent['blood_group']],
                                ['label' => 'Phone',        'value' => $modalStudent['phone']],
                                ['label' => 'Date of Birth','value' => $modalStudent['dob']],
                                ['label' => 'Admission Year','value' => $modalStudent['admission_year']],
                                ['label' => 'Email',        'value' => $modalStudent['email']],
                            ] as $field)
                                <div class="rounded-lg bg-gray-50 px-3 py-2.5 dark:bg-gray-800/60">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $field['label'] }}</div>
                                    <div class="mt-0.5 text-sm font-medium text-gray-900 dark:text-gray-100 break-words">
                                        {{ $field['value'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Attendance summary --}}
                    <div>
                        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Attendance Summary
                        </h3>
                        @php
                            $pct = $modalAttendance['percentage'] ?? 0;
                            $pctColor = $pct >= 75 ? 'text-green-700 dark:text-green-400'
                                : ($pct >= 60 ? 'text-yellow-700 dark:text-yellow-400'
                                : 'text-red-700 dark:text-red-400');
                        @endphp
                        <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
                            @foreach ([
                                ['label' => 'Present',    'value' => $modalAttendance['present'],  'color' => 'text-green-700 dark:text-green-400',  'bg' => 'bg-green-50 dark:bg-green-900/20'],
                                ['label' => 'Absent',     'value' => $modalAttendance['absent'],   'color' => 'text-red-700 dark:text-red-400',     'bg' => 'bg-red-50 dark:bg-red-900/20'],
                                ['label' => 'Late',       'value' => $modalAttendance['late'],     'color' => 'text-yellow-700 dark:text-yellow-400','bg' => 'bg-yellow-50 dark:bg-yellow-900/20'],
                                ['label' => 'Excused',    'value' => $modalAttendance['excused'],  'color' => 'text-blue-700 dark:text-blue-400',   'bg' => 'bg-blue-50 dark:bg-blue-900/20'],
                                ['label' => 'Total Days', 'value' => $modalAttendance['total'],    'color' => 'text-gray-700 dark:text-gray-300',   'bg' => 'bg-gray-50 dark:bg-gray-800/60'],
                                ['label' => 'Percentage', 'value' => $pct.'%',                    'color' => $pctColor,                            'bg' => 'bg-gray-50 dark:bg-gray-800/60'],
                            ] as $stat)
                                <div class="rounded-lg {{ $stat['bg'] }} px-3 py-2.5 text-center">
                                    <div class="text-lg font-bold {{ $stat['color'] }}">{{ $stat['value'] }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Recent exam scores --}}
                    <div>
                        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Recent Exam Scores
                        </h3>
                        @if (count($modalScores) > 0)
                            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50">
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Exam</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Subject</th>
                                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">Marks</th>
                                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">%</th>
                                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                        @foreach ($modalScores as $score)
                                            @php
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
                                                ];
                                                $gradeClass = $gradeColors[$score['grade']] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400';
                                            @endphp
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                                <td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-300">{{ $score['exam_group'] }}</td>
                                                <td class="px-3 py-2 text-xs font-medium text-gray-900 dark:text-gray-100">{{ $score['subject'] }}</td>
                                                <td class="px-3 py-2 text-center text-xs">
                                                    @if ($score['absent'])
                                                        <span class="rounded-full bg-orange-100 px-2 py-0.5 text-xs font-bold text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">AB</span>
                                                    @else
                                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $score['marks'] }}</span>
                                                        <span class="text-gray-400">/{{ $score['max_marks'] }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 text-center text-xs text-gray-600 dark:text-gray-400">
                                                    {{ $score['percentage'] !== null ? $score['percentage'].'%' : '—' }}
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    <span class="inline-block rounded px-2 py-0.5 text-xs font-semibold {{ $gradeClass }}">
                                                        {{ $score['grade'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="rounded-lg bg-gray-50 py-4 text-center text-xs text-gray-400 dark:bg-gray-800/40 dark:text-gray-500">
                                No published exam results yet.
                            </p>
                        @endif
                    </div>

                </div>

                {{-- Modal footer --}}
                <div class="sticky bottom-0 flex justify-end rounded-b-2xl border-t border-gray-200 bg-white px-6 py-3 dark:border-gray-700 dark:bg-gray-900">
                    <button
                        wire:click="closeModal"
                        class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors"
                    >
                        Close
                    </button>
                </div>

            </div>
        </div>
    @endif

</x-filament-panels::page>

<x-filament-panels::page>
    <div class="space-y-4">
        <x-filament::section>
            <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
                <div class="flex min-w-[180px] flex-col gap-1">
                    <label class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Attendance Date
                    </label>
                    <input
                        type="date"
                        wire:model.live="attendanceDate"
                        max="{{ now()->toDateString() }}"
                        class="block rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    />
                </div>

                @if (count($facultyClasses) > 1)
                    <div class="flex min-w-[200px] flex-1 flex-col gap-1">
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Class
                        </label>
                        <select
                            wire:model.live="selectedClassId"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                        >
                            <option value="">— Choose a class —</option>
                            @foreach ($facultyClasses as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            @if ($attendanceDate && $attendanceDate !== now()->toDateString())
                <div class="mt-4 flex flex-col gap-1">
                    <label class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">
                        Reason for Backdating <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-start gap-2 rounded-lg border border-amber-300 bg-amber-50 p-3 dark:border-amber-600 dark:bg-amber-900/20">
                        <x-heroicon-o-exclamation-triangle class="mt-0.5 h-4 w-4 flex-shrink-0 text-amber-600 dark:text-amber-400" />
                        <div class="flex-1">
                            <p class="mb-2 text-xs text-amber-700 dark:text-amber-300">
                                You are marking attendance for a past date
                                ({{ \Carbon\Carbon::parse($attendanceDate)->format('d M Y') }}).
                                A reason is required.
                            </p>
                            <textarea
                                wire:model.live="editReason"
                                rows="2"
                                placeholder="e.g. Network outage prevented marking on time…"
                                class="block w-full rounded-lg border border-amber-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-400 dark:border-amber-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                            ></textarea>
                        </div>
                    </div>
                </div>
            @endif
        </x-filament::section>

        @if (empty($facultyClasses))
            <x-filament::section>
                <div class="flex items-center gap-3 text-warning-600 dark:text-warning-400">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 flex-shrink-0" />
                    <p class="text-sm">No classes assigned. Please contact the administrator to add your timetable.</p>
                </div>
            </x-filament::section>
        @endif

        @if ($selectedClassId)
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-center dark:border-green-700 dark:bg-green-900/30">
                    <div class="text-3xl font-bold text-green-700 dark:text-green-400">
                        {{ $this->getPresentCount() }}
                    </div>
                    <div class="mt-0.5 text-xs font-semibold uppercase tracking-wide text-green-600 dark:text-green-500">
                        Present
                    </div>
                </div>

                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-center dark:border-red-700 dark:bg-red-900/30">
                    <div class="text-3xl font-bold text-red-700 dark:text-red-400">
                        {{ $this->getAbsentCount() }}
                    </div>
                    <div class="mt-0.5 text-xs font-semibold uppercase tracking-wide text-red-600 dark:text-red-500">
                        Absent
                    </div>
                </div>

                <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-center dark:border-yellow-700 dark:bg-yellow-900/30">
                    <div class="text-3xl font-bold text-yellow-700 dark:text-yellow-400">
                        {{ $this->getLateCount() }}
                    </div>
                    <div class="mt-0.5 text-xs font-semibold uppercase tracking-wide text-yellow-600 dark:text-yellow-500">
                        Late
                    </div>
                </div>

                <div class="rounded-xl border border-purple-200 bg-purple-50 p-4 text-center dark:border-purple-700 dark:bg-purple-900/30">
                    <div class="text-3xl font-bold text-purple-700 dark:text-purple-400">
                        {{ $this->getExcusedCount() }}
                    </div>
                    <div class="mt-0.5 text-xs font-semibold uppercase tracking-wide text-purple-600 dark:text-purple-500">
                        Excused
                    </div>
                </div>
            </div>

            @if ($alreadyMarked)
                <div class="flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-700 dark:bg-blue-900/20">
                    <x-heroicon-o-information-circle class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400" />
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        Attendance is already recorded for this date. Saving will
                        <strong>update</strong> the existing records.
                    </p>
                </div>
            @endif

            <x-filament::section>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            {{ $facultyClasses[$selectedClassId] ?? '' }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($attendanceDate)->format('l, d F Y') }}
                            — {{ count($students) }} students
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            wire:click="markAllPresent"
                            aria-label="Mark every student present"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-green-700 active:bg-green-800"
                        >
                            <x-heroicon-m-check class="h-3.5 w-3.5" />
                            All Present
                        </button>
                        <button
                            type="button"
                            wire:click="markAllAbsent"
                            aria-label="Mark every student absent"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-red-700 active:bg-red-800"
                        >
                            <x-heroicon-m-x-mark class="h-3.5 w-3.5" />
                            All Absent
                        </button>
                    </div>
                </div>
            </x-filament::section>

            @if (count($students) > 0)
                <x-filament::section>
                    <div class="-mx-6 -my-6 divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($students as $student)
                            @php $status = $attendance[$student['id']] ?? 'present'; @endphp

                            <div class="flex items-center gap-4 px-6 py-3 {{ $loop->even ? 'bg-gray-50 dark:bg-gray-800/50' : '' }}">
                                <div class="min-w-0 flex-1">
                                    <span class="block font-mono text-xs font-medium text-gray-400 dark:text-gray-500">
                                        {{ $student['roll_number'] }}
                                    </span>
                                    <span class="block truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $student['name'] }}
                                    </span>
                                </div>

                                <div class="grid flex-shrink-0 grid-cols-2 gap-1.5 sm:flex sm:flex-wrap">
                                    <button
                                        type="button"
                                        wire:click="setStatus({{ $student['id'] }}, 'present')"
                                        aria-pressed="{{ $status === 'present' ? 'true' : 'false' }}"
                                        aria-label="Set {{ $student['name'] }} attendance to Present. Current status: {{ $this->getStatusLabel($status) }}."
                                        class="inline-flex min-h-[44px] w-full items-center justify-center gap-1.5 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all sm:w-auto
                                            {{ $status === 'present'
                                                ? 'bg-green-600 text-white shadow-md ring-2 ring-green-400 ring-offset-1 dark:ring-offset-gray-900'
                                                : 'bg-gray-100 text-gray-700 hover:bg-green-50 hover:text-green-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-green-900/30 dark:hover:text-green-300' }}"
                                    >
                                        <x-heroicon-o-check-circle class="h-4 w-4" />
                                        <span>Present</span>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="setStatus({{ $student['id'] }}, 'absent')"
                                        aria-pressed="{{ $status === 'absent' ? 'true' : 'false' }}"
                                        aria-label="Set {{ $student['name'] }} attendance to Absent. Current status: {{ $this->getStatusLabel($status) }}."
                                        class="inline-flex min-h-[44px] w-full items-center justify-center gap-1.5 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all sm:w-auto
                                            {{ $status === 'absent'
                                                ? 'bg-red-600 text-white shadow-md ring-2 ring-red-400 ring-offset-1 dark:ring-offset-gray-900'
                                                : 'bg-gray-100 text-gray-700 hover:bg-red-50 hover:text-red-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-red-900/30 dark:hover:text-red-300' }}"
                                    >
                                        <x-heroicon-o-x-circle class="h-4 w-4" />
                                        <span>Absent</span>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="setStatus({{ $student['id'] }}, 'late')"
                                        aria-pressed="{{ $status === 'late' ? 'true' : 'false' }}"
                                        aria-label="Set {{ $student['name'] }} attendance to Late. Current status: {{ $this->getStatusLabel($status) }}."
                                        class="inline-flex min-h-[44px] w-full items-center justify-center gap-1.5 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all sm:w-auto
                                            {{ $status === 'late'
                                                ? 'bg-yellow-500 text-white shadow-md ring-2 ring-yellow-400 ring-offset-1 dark:ring-offset-gray-900'
                                                : 'bg-gray-100 text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-yellow-900/30 dark:hover:text-yellow-300' }}"
                                    >
                                        <x-heroicon-o-clock class="h-4 w-4" />
                                        <span>Late</span>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="setStatus({{ $student['id'] }}, 'excused')"
                                        aria-pressed="{{ $status === 'excused' ? 'true' : 'false' }}"
                                        aria-label="Set {{ $student['name'] }} attendance to Excused. Current status: {{ $this->getStatusLabel($status) }}."
                                        class="inline-flex min-h-[44px] w-full items-center justify-center gap-1.5 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all sm:w-auto
                                            {{ $status === 'excused'
                                                ? 'bg-purple-600 text-white shadow-md ring-2 ring-purple-400 ring-offset-1 dark:ring-offset-gray-900'
                                                : 'bg-gray-100 text-gray-700 hover:bg-purple-50 hover:text-purple-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-purple-900/30 dark:hover:text-purple-300' }}"
                                    >
                                        <x-heroicon-o-shield-check class="h-4 w-4" />
                                        <span>Excused</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>

                <div class="sticky bottom-4 z-10">
                    <button
                        type="button"
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="cursor-not-allowed opacity-75"
                        aria-label="{{ $alreadyMarked ? 'Update attendance records' : 'Save attendance records' }}"
                        class="w-full rounded-xl bg-primary-600 px-6 py-4 text-base font-bold text-white shadow-lg transition-colors hover:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-400/50"
                    >
                        <span wire:loading.remove wire:target="submit">
                            @if ($alreadyMarked)
                                Update Attendance
                            @else
                                Save Attendance
                            @endif
                        </span>
                        <span wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Saving…
                        </span>
                    </button>
                </div>

            @else
                <x-filament::section>
                    <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                        No students found in this class.
                    </p>
                </x-filament::section>
            @endif
        @endif
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    {{-- Filters --}}
    <x-filament::section>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            {{ $this->form }}
        </div>
    </x-filament::section>

    @if ($defaulters->isEmpty())
        <x-filament::section>
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-500/10">
                    <x-filament::icon
                        icon="heroicon-o-check-circle"
                        class="h-8 w-8 text-emerald-600 dark:text-emerald-400"
                    />
                </div>
                <p class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">All students are above 75%</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No attendance defaulters at this time.</p>
            </div>
        </x-filament::section>
    @else
        {{-- Summary banner --}}
        <div class="rounded-xl border border-red-200 bg-red-50 p-5 dark:border-red-500/20 dark:bg-red-500/10">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/20">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-5 w-5 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="font-semibold text-red-800 dark:text-red-300">
                        {{ $defaulters->count() }} student(s) are below the 75% attendance threshold.
                    </p>
                    <p class="text-sm text-red-600 dark:text-red-400">
                        Use the "Notify All Parents" button above to send bulk email warnings.
                    </p>
                </div>
            </div>
        </div>

        <x-filament::section>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 dark:text-gray-200">Student</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 dark:text-gray-200">Roll No</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 dark:text-gray-200">Class</th>
                            <th class="py-3 px-4 text-center font-semibold text-gray-700 dark:text-gray-200">Present</th>
                            <th class="py-3 px-4 text-center font-semibold text-gray-700 dark:text-gray-200">Absent</th>
                            <th class="py-3 px-4 text-center font-semibold text-gray-700 dark:text-gray-200">Total</th>
                            <th class="py-3 px-4 text-center font-semibold text-gray-700 dark:text-gray-200">Attendance %</th>
                            <th class="py-3 px-4 text-center font-semibold text-gray-700 dark:text-gray-200">Days Needed</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 dark:text-gray-200">Guardian</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 dark:text-gray-200">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($defaulters as $student)
                            @php
                                $pct = $student->attendance_percentage;
                                $pctClass = $pct >= 60
                                    ? 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:border-amber-500/20'
                                    : 'bg-red-50 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-300 dark:border-red-500/20';
                                $guardian = $student->guardians->firstWhere('is_primary_contact', true)
                                    ?? $student->guardians->first();
                            @endphp
                            <tr class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-800 dark:even:bg-gray-700/30">
                                <td class="py-3 px-4 font-medium text-gray-900 dark:text-gray-100">
                                    {{ $student->user?->name ?? $student->roll_number }}
                                </td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                    {{ $student->roll_number }}
                                </td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                    {{ $student->collegeClass?->name ?? 'N/A' }}
                                </td>
                                <td class="py-3 px-4 text-center text-emerald-700 font-semibold dark:text-emerald-400">
                                    {{ $student->present_count }}
                                </td>
                                <td class="py-3 px-4 text-center text-red-700 font-semibold dark:text-red-400">
                                    {{ $student->absent_count }}
                                </td>
                                <td class="py-3 px-4 text-center text-gray-600 dark:text-gray-300">
                                    {{ $student->total_count }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $pctClass }}">
                                        {{ $pct }}%
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if ($student->shortfall > 0)
                                        <span class="inline-flex items-center rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700 dark:bg-orange-500/10 dark:text-orange-300">
                                            +{{ $student->shortfall }} days
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if ($guardian)
                                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $guardian->fullName() }}</p>
                                        @if ($guardian->email)
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $guardian->email }}</p>
                                        @else
                                            <p class="text-xs text-gray-400 dark:text-gray-500 italic">No email</p>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">No guardian</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if ($guardian && $guardian->email)
                                        <button
                                            wire:click="notifyParent({{ $student->id }})"
                                            wire:loading.attr="disabled"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100 disabled:opacity-50 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-300 dark:hover:bg-red-500/20"
                                        >
                                            <x-filament::icon icon="heroicon-o-envelope" class="h-3.5 w-3.5" />
                                            Notify
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">No email</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>

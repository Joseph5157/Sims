<x-filament-panels::page>
    <div class="space-y-6">

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="flex flex-wrap items-center gap-4">

                <div class="min-w-[220px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Select Class</label>
                    <select
                        wire:model.live="college_class_id"
                        class="mt-1 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                    >
                        <option value="">Choose a class</option>
                        @foreach ($classOptions as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @foreach ($this->getMonthTabs() as $value => $label)
                        <button
                            type="button"
                            wire:click="selectMonth('{{ $value }}')"
                            class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $month === $value ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white hover:bg-amber-600' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            @if (! $college_class_id)
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    Select a class to load the monthly attendance grid.
                </div>
            @elseif (count($students) === 0)
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    No students found for this class.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-max border-collapse border border-gray-200 w-full">
                        <thead>
                            <tr>
                                <th class="sticky left-0 z-10 bg-white px-6 py-4 text-left text-sm font-semibold text-gray-700 border-b border-r border-gray-200 dark:bg-gray-900 dark:text-gray-200">
                                    Student
                                </th>
                                @foreach ($days as $day)
                                    <th class="px-3 py-4 text-center text-xs font-semibold text-gray-600 border-b border-r border-gray-200 dark:text-gray-300">
                                        {{ $day['label'] }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="sticky left-0 z-10 bg-white px-6 py-4 text-sm font-medium text-gray-800 border-b border-r border-gray-200 dark:bg-gray-900 dark:text-gray-100">
                                        <div class="flex flex-col">
                                            <span>{{ $student['name'] }}</span>
                                            <span class="text-xs text-gray-500">{{ $student['roll_number'] }}</span>
                                        </div>
                                    </td>
                                    @foreach ($days as $day)
                                        @php
                                            $status = $attendance[$student['id']][$day['date']] ?? 'absent';
                                            $isPresent = $status === 'present';
                                        @endphp
                                        <td
                                            wire:click="toggleAttendance({{ $student['id'] }}, '{{ $day['date'] }}')"
                                            class="px-3 py-4 text-center cursor-pointer border-b border-r border-gray-200 hover:bg-gray-50 {{ $isPresent ? 'bg-orange-500 text-white' : 'bg-white' }}"
                                        >
                                            @if ($isPresent)
                                                <span class="text-lg font-bold">✓</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Filters</x-slot>
            {{ $this->form }}
        </x-filament::section>

        @if (! empty($this->rows))
            <x-filament::section heading="Attendance Grid">
                <div class="overflow-x-auto">
                    <table style="min-width: 1200px" class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <th class="whitespace-nowrap px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-200">Student</th>
                                <th class="whitespace-nowrap px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-200">Roll No</th>

                                @foreach ($this->days as $day)
                                    <th class="whitespace-nowrap px-2 py-3 text-center font-medium text-gray-700 dark:text-gray-200">
                                        {{ $day }}
                                    </th>
                                @endforeach

                                <th class="whitespace-nowrap px-4 py-3 text-center font-medium text-gray-700 dark:text-gray-200">Total P</th>
                                <th class="whitespace-nowrap px-4 py-3 text-center font-medium text-gray-700 dark:text-gray-200">Total A</th>
                                <th class="whitespace-nowrap px-4 py-3 text-center font-medium text-gray-700 dark:text-gray-200">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->rows as $row)
                                @php
                                    $student = $row['student'];
                                    $cells = $row['cells'];
                                @endphp

                                <tr class="border-b border-gray-100 odd:bg-white even:bg-gray-50 dark:border-gray-700 dark:odd:bg-gray-800 dark:even:bg-gray-700/40">
                                    <td class="whitespace-nowrap px-4 py-2 font-medium">
                                        {{ $student->user?->name ?? $student->roll_number }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-500 dark:text-gray-300">
                                        {{ $student->roll_number }}
                                    </td>

                                    @foreach ($this->days as $day)
                                        @php
                                            $value = $cells[$day] ?? '-';
                                            $badgeClass = match ($value) {
                                                'P' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
                                                'A' => 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-300',
                                                default => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300',
                                            };
                                        @endphp
                                        <td class="px-2 py-2 text-center">
                                            <span class="inline-flex w-7 justify-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $badgeClass }}">
                                                {{ $value }}
                                            </span>
                                        </td>
                                    @endforeach

                                    <td class="whitespace-nowrap px-4 py-2 text-center font-medium">{{ $row['present'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-2 text-center font-medium">{{ $row['absent'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-2 text-center font-medium">{{ $row['percentage'] }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @elseif (($this->data['college_class_id'] ?? null) && ($this->data['month'] ?? null) && ($this->data['year'] ?? null))
            <x-filament::section>
                <p class="py-8 text-center text-gray-500 dark:text-gray-300">No students or attendance records found for this selection.</p>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>

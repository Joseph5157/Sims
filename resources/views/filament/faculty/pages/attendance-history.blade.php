<x-filament-panels::page>
    <div class="space-y-4">

        {{-- Filter --}}
        @if (count($facultyClasses) > 1)
            <x-filament::section>
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">
                        Filter by Class:
                    </label>
                    <select
                        wire:model.live="filterClassId"
                        class="block w-full sm:max-w-xs rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    >
                        <option value="">— All Classes —</option>
                        @foreach ($facultyClasses as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </x-filament::section>
        @endif

        {{-- History Table --}}
        <x-filament::section heading="Last 30 Days">
            @if (count($history) > 0)
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Class</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wide">Present</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wide">Absent</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-orange-600 dark:text-orange-400 uppercase tracking-wide">Late</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Total</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @foreach ($history as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                    <td class="px-6 py-3.5 font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                        {{ $row['date'] }}
                                    </td>
                                    <td class="px-6 py-3.5 text-gray-700 dark:text-gray-300">
                                        {{ $row['class_name'] }}
                                    </td>
                                    <td class="px-6 py-3.5 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                            {{ $row['present'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                            {{ $row['absent'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                                            {{ $row['late'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-center text-gray-500 dark:text-gray-400 font-medium">
                                        {{ $row['total'] }}
                                    </td>
                                    <td class="px-6 py-3.5 text-right">
                                        <a
                                            href="{{ \App\Filament\Faculty\Pages\MarkAttendance::getUrl() }}"
                                            class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 dark:text-primary-400 hover:underline"
                                        >
                                            <x-heroicon-m-pencil class="w-3.5 h-3.5" />
                                            Re-mark
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">
                    No attendance records found for the last 30 days.
                </p>
            @endif
        </x-filament::section>

    </div>
</x-filament-panels::page>

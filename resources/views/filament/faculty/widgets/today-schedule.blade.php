<x-filament-widgets::widget>
    <x-filament::section heading="Today's Schedule ({{ $today }})">
        @if ($slots->isEmpty())
            <p class="py-4 text-center text-gray-500">No classes scheduled for today.</p>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-3 py-2 text-left">Period</th>
                        <th class="px-3 py-2 text-left">Subject</th>
                        <th class="px-3 py-2 text-left">Class</th>
                        <th class="px-3 py-2 text-left">Room</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($slots as $slot)
                        <tr class="border-b border-gray-100 last:border-0 dark:border-gray-700">
                            <td class="px-3 py-2">{{ $slot->period }}</td>
                            <td class="px-3 py-2">{{ $slot->subject?->name ?? 'N/A' }}</td>
                            <td class="px-3 py-2">{{ $slot->collegeClass?->name ?? 'N/A' }}</td>
                            <td class="px-3 py-2">{{ $slot->room ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>


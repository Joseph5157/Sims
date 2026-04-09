<x-filament-panels::page>
    <h1 class="text-2xl font-bold mb-6">My Timetable</h1>

    @if($timetableSlots->isEmpty())
        <p class="text-gray-500">No timetable slots assigned to you.</p>
    @else
        @foreach($timetableSlots as $day => $slots)
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">{{ $day }}</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-2 px-4 border-b">Period</th>
                                <th class="py-2 px-4 border-b">Subject</th>
                                <th class="py-2 px-4 border-b">Class</th>
                                <th class="py-2 px-4 border-b">Room</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($slots->sortBy('period') as $slot)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-4">{{ $slot->period }}</td>
                                    <td class="py-2 px-4">{{ $slot->subject->name ?? 'N/A' }}</td>
                                    <td class="py-2 px-4">{{ $slot->collegeClass->name ?? 'N/A' }}</td>
                                    <td class="py-2 px-4">{{ $slot->room ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif
</x-filament-panels::page>

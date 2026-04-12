<x-filament-widgets::widget>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-filament::section heading="Recent Students">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left dark:bg-gray-700">
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200">Student</th>
                            <th class="whitespace-nowrap px-4 py-3 font-medium text-gray-700 dark:text-gray-200">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentStudents as $student)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="px-4 py-2 font-medium">
                                    {{ $student->user?->name ?? $student->roll_number }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-500 dark:text-gray-300">
                                    {{ $student->created_at?->format('d M Y, g:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-300" colspan="2">No students yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <x-filament::section heading="Recent Discipline Cases">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left dark:bg-gray-700">
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200">Title</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200">Student</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200">Severity</th>
                            <th class="whitespace-nowrap px-4 py-3 font-medium text-gray-700 dark:text-gray-200">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentDisciplineCases as $case)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="px-4 py-2 font-medium">{{ $case->title }}</td>
                                <td class="px-4 py-2">
                                    {{ $case->student?->user?->name ?? $case->student?->roll_number }}
                                </td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                        {{ $case->severity }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-500 dark:text-gray-300">
                                    {{ $case->created_at?->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-300" colspan="4">No discipline cases yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <x-filament::section heading="Upcoming Exams">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left dark:bg-gray-700">
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200">Date</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200">Subject</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200">Exam Group</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($upcomingExams as $exam)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="whitespace-nowrap px-4 py-2 font-medium">
                                    {{ $exam->date?->format('d M Y') }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $exam->subject?->name ?? 'Subject' }}
                                </td>
                                <td class="px-4 py-2 text-gray-500 dark:text-gray-300">
                                    {{ $exam->examGroup?->name ?? 'Exam Group' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-300" colspan="3">No upcoming exams.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-widgets::widget>


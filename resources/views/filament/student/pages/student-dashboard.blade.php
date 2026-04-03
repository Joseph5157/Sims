<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Attendance Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @if($student)
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Attendance</p>
                            <p class="text-4xl font-semibold mt-1">
                                {{ $student->attendance_percentage }}<span class="text-2xl">%</span>
                            </p>
                        </div>
                        <div class="text-5xl">
                            @if($student->attendance_percentage < 75)
                                ⚠️
                            @else
                                ✅
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div
                            class="h-full rounded-full transition-all duration-300
                                {{ $student->attendance_percentage < 75 ? 'bg-red-500' : 'bg-emerald-500' }}"
                            style="width: {{ $student->attendance_percentage }}%">
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Recent Grades -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-lg mb-4">Recent Grades</h3>
            @if($recentGrades->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-3 px-4">Subject</th>
                                <th class="text-left py-3 px-4">Exam Type</th>
                                <th class="text-right py-3 px-4">Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentGrades as $grade)
                                <tr class="border-b dark:border-gray-700 last:border-none">
                                    <td class="py-3 px-4">{{ $grade->subject?->name ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">{{ ucfirst(str_replace('_', ' ', $grade->exam_type)) }}</td>
                                    <td class="py-3 px-4 text-right font-medium">
                                        {{ $grade->marks_obtained }} / {{ $grade->total_marks }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 py-8 text-center">No grades recorded yet.</p>
            @endif
        </div>

        <!-- Recent Notices -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-lg mb-4">Recent Notices</h3>
            @if($recentNotices->isNotEmpty())
                <div class="space-y-4">
                    @foreach($recentNotices as $notice)
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-amber-100 dark:bg-amber-900 rounded-full flex items-center justify-center text-amber-600">
                                📢
                            </div>
                            <div>
                                <p class="font-medium">{{ $notice->title }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $notice->content }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $notice->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 py-8 text-center">No active notices.</p>
            @endif
        </div>
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>

    @if ($exams->isNotEmpty() && $students->isNotEmpty())
        <x-filament::section heading='Gradebook'>
            <div class='overflow-x-auto'>
                <table class='min-w-max w-full text-sm'>
                    <thead>
                        <tr class='bg-gray-100 dark:bg-gray-700'>
                            <th class='whitespace-nowrap px-4 py-3 text-left'>Student</th>
                            <th class='whitespace-nowrap px-4 py-3 text-left'>Roll No</th>
                            @foreach ($exams as $exam)
                                <th class='min-w-[180px] px-4 py-3 text-center'>
                                    {{ $exam->subject?->name }}<br>
                                    <span class='text-xs font-normal text-gray-500'>({{ $exam->maximum_marks }} marks)</span>
                                </th>
                            @endforeach
                            <th class='whitespace-nowrap px-4 py-3 text-center'>Total</th>
                            <th class='whitespace-nowrap px-4 py-3 text-center'>%</th>
                            <th class='whitespace-nowrap px-4 py-3 text-center'>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            @php
                                $totalObtained = 0;
                                $totalMax = 0;
                                foreach ($exams as $exam) {
                                    $score = $scores[$student->id][$exam->id] ?? null;
                                    if ($score && ! $score->absent && $score->marks_obtained !== null) {
                                        $totalObtained += $score->marks_obtained;
                                        $totalMax += $exam->maximum_marks;
                                    }
                                }
                                $overallPct = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 1) : 0;
                                $overallGrade = $totalMax > 0 ? \App\Models\GradingLevel::calculateGrade($overallPct, $college_class_id) : null;
                            @endphp
                            <tr class='border-b border-gray-100 odd:bg-white even:bg-gray-50 dark:border-gray-700 dark:odd:bg-gray-800 dark:even:bg-gray-700/40'>
                                <td class='whitespace-nowrap px-4 py-2 font-medium'>{{ $student->user?->name }}</td>
                                <td class='whitespace-nowrap px-4 py-2 text-gray-500'>{{ $student->roll_number }}</td>
                                @foreach ($exams as $exam)
                                    @php
                                        $score = $scores[$student->id][$exam->id] ?? null;
                                        $cellClass = 'min-w-[180px] px-4 py-2 text-center';

                                        if (! $score) {
                                            $badgeClass = 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400';
                                        } elseif ($score->absent) {
                                            $badgeClass = 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300';
                                        } elseif ($score->marks_obtained >= $exam->minimum_marks) {
                                            $badgeClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300';
                                        } else {
                                            $badgeClass = 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-300';
                                        }
                                    @endphp
                                    <td class='{{ $cellClass }}'>
                                        <span class='inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $badgeClass }}'>
                                            {{ $score ? ($score->absent ? 'AB' : $score->marks_obtained) : '-' }}
                                        </span>
                                    </td>
                                @endforeach
                                <td class='whitespace-nowrap px-4 py-2 text-center font-medium'>{{ $totalObtained }}/{{ $totalMax }}</td>
                                <td class='whitespace-nowrap px-4 py-2 text-center'>{{ $overallPct }}%</td>
                                <td class='whitespace-nowrap px-4 py-2 text-center'>
                                    <span class='inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-300'>
                                        {{ $overallGrade?->name ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @elseif ($college_class_id && $exam_group_id)
        <x-filament::section>
            <p class='py-8 text-center text-gray-500'>No students or exams found for this selection.</p>
        </x-filament::section>
    @endif
</x-filament-panels::page>

<x-filament-panels::page>

{{-- ========================================================================== --}}
{{-- NO PROFILE STATE                                                            --}}
{{-- ========================================================================== --}}
@if (! $hasProfile)
    <div class="flex flex-col items-center justify-center gap-4 py-24 text-center">
        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-violet-100 dark:bg-violet-900/30">
            <x-heroicon-o-user-circle class="h-10 w-10 text-violet-500" />
        </div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">No Student Profile Found</h2>
        <p class="max-w-sm text-sm text-gray-500 dark:text-gray-400">
            Your account is not linked to a student profile yet. Please contact the administrator.
        </p>
    </div>

@else

{{-- ========================================================================== --}}
{{-- PROFILE HEADER CARD                                                         --}}
{{-- ========================================================================== --}}
<div class="overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-violet-600 to-purple-700 shadow-lg">
    <div class="relative px-6 py-6">
        {{-- Decorative circles --}}
        <div class="pointer-events-none absolute -right-10 -top-10 h-48 w-48 rounded-full bg-white/5"></div>
        <div class="pointer-events-none absolute -bottom-8 right-28 h-28 w-28 rounded-full bg-white/5"></div>

        <div class="relative flex flex-col items-center gap-5 sm:flex-row sm:items-start">

            {{-- Avatar --}}
            <div class="flex-shrink-0">
                @if ($header['photo_url'])
                    <img
                        src="{{ $header['photo_url'] }}"
                        alt="{{ $header['name'] }}"
                        class="h-24 w-24 rounded-full object-cover ring-4 ring-white/30 shadow-lg"
                    />
                @else
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-white/20 ring-4 ring-white/30 shadow-lg backdrop-blur-sm">
                        <span class="text-4xl font-bold text-white">{{ $header['initials'] }}</span>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="min-w-0 flex-1 text-center sm:text-left">
                {{-- Name + status --}}
                <div class="flex flex-wrap items-center justify-center gap-2 sm:justify-start">
                    <h1 class="text-2xl font-bold text-white">{{ $header['name'] }}</h1>
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $header['status_class'] }}">
                        {{ $header['status_label'] }}
                    </span>
                </div>

                {{-- Class + Dept --}}
                <p class="mt-1 text-sm text-violet-200">
                    {{ $header['class'] }}
                    @if ($header['department'] !== '—')
                        <span class="opacity-60">·</span> {{ $header['department'] }}
                    @endif
                </p>

                {{-- Badges row --}}
                <div class="mt-3 flex flex-wrap items-center justify-center gap-2 sm:justify-start">
                    @if ($header['roll_number'] !== '—')
                        <span class="flex items-center gap-1 rounded-full bg-white/15 px-3 py-1 text-xs font-medium text-white backdrop-blur-sm">
                            <x-heroicon-m-identification class="h-3.5 w-3.5" />
                            Roll: {{ $header['roll_number'] }}
                        </span>
                    @endif
                    @if ($header['admission_no'] !== '—')
                        <span class="flex items-center gap-1 rounded-full bg-white/15 px-3 py-1 text-xs font-medium text-white backdrop-blur-sm">
                            <x-heroicon-m-document-text class="h-3.5 w-3.5" />
                            Adm: {{ $header['admission_no'] }}
                        </span>
                    @endif
                    @if ($header['academic_year'] !== '—')
                        <span class="flex items-center gap-1 rounded-full bg-white/15 px-3 py-1 text-xs font-medium text-white backdrop-blur-sm">
                            <x-heroicon-m-calendar class="h-3.5 w-3.5" />
                            {{ $header['academic_year'] }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================== --}}
{{-- TWO-COLUMN LAYOUT: personal + academic                                      --}}
{{-- ========================================================================== --}}
<div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

    {{-- ------------------------------------------------------------------ --}}
    {{-- PERSONAL INFORMATION                                                 --}}
    {{-- ------------------------------------------------------------------ --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
        {{-- Card header --}}
        <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/30">
                <x-heroicon-o-user class="h-4 w-4 text-violet-600 dark:text-violet-400" />
            </div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Personal Information</h2>
        </div>

        <div class="divide-y divide-gray-50 dark:divide-gray-800">
            @foreach ([
                ['label' => 'Date of Birth',   'value' => $personal['date_of_birth'],  'icon' => 'heroicon-o-cake'],
                ['label' => 'Gender',          'value' => $personal['gender'],         'icon' => 'heroicon-o-user'],
                ['label' => 'Blood Group',     'value' => $personal['blood_group'],    'icon' => 'heroicon-o-heart'],
                ['label' => 'Phone',           'value' => $personal['phone'],          'icon' => 'heroicon-o-phone'],
                ['label' => 'Email',           'value' => $personal['email'],          'icon' => 'heroicon-o-envelope'],
                ['label' => 'Address',         'value' => $personal['address'],        'icon' => 'heroicon-o-map-pin'],
                ['label' => 'Admission Year',  'value' => $personal['admission_year'], 'icon' => 'heroicon-o-calendar-days'],
            ] as $row)
                <div class="flex items-start gap-3 px-5 py-3">
                    <div class="mt-0.5 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-md bg-gray-50 dark:bg-gray-800">
                        <x-dynamic-component :component="$row['icon']" class="h-3.5 w-3.5 text-gray-400 dark:text-gray-500" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $row['label'] }}</p>
                        <p class="mt-0.5 text-sm font-medium text-gray-800 dark:text-gray-200 break-words">
                            {{ $row['value'] ?: '—' }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ------------------------------------------------------------------ --}}
    {{-- ACADEMIC INFORMATION                                                 --}}
    {{-- ------------------------------------------------------------------ --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
        {{-- Card header --}}
        <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                <x-heroicon-o-academic-cap class="h-4 w-4 text-blue-600 dark:text-blue-400" />
            </div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Academic Information</h2>
        </div>

        <div class="divide-y divide-gray-50 dark:divide-gray-800">
            @foreach ([
                ['label' => 'Class',           'value' => $academic['class'],          'icon' => 'heroicon-o-building-library'],
                ['label' => 'Section',         'value' => $academic['section'],        'icon' => 'heroicon-o-squares-2x2'],
                ['label' => 'Semester',        'value' => $academic['semester'],       'icon' => 'heroicon-o-arrow-path'],
                ['label' => 'Department',      'value' => $academic['department'],     'icon' => 'heroicon-o-building-office'],
                ['label' => 'Academic Year',   'value' => $academic['academic_year'],  'icon' => 'heroicon-o-calendar'],
                ['label' => 'Roll Number',     'value' => $academic['roll_number'],    'icon' => 'heroicon-o-identification'],
                ['label' => 'Admission No.',   'value' => $academic['admission_no'],   'icon' => 'heroicon-o-document-text'],
                ['label' => 'Admission Year',  'value' => $academic['admission_year'], 'icon' => 'heroicon-o-calendar-days'],
            ] as $row)
                <div class="flex items-start gap-3 px-5 py-3">
                    <div class="mt-0.5 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-md bg-gray-50 dark:bg-gray-800">
                        <x-dynamic-component :component="$row['icon']" class="h-3.5 w-3.5 text-gray-400 dark:text-gray-500" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $row['label'] }}</p>
                        <p class="mt-0.5 text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ $row['value'] ?: '—' }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ========================================================================== --}}
{{-- QUICK STATS                                                                  --}}
{{-- ========================================================================== --}}
<div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
    <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
            <x-heroicon-o-chart-bar class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
        </div>
        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Quick Stats</h2>
    </div>

    <div class="grid grid-cols-1 divide-y divide-gray-50 sm:grid-cols-3 sm:divide-x sm:divide-y-0 dark:divide-gray-800">

        {{-- Attendance --}}
        <div class="flex flex-col items-center justify-center gap-2 px-6 py-5 text-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-violet-100 dark:bg-violet-900/30">
                <x-heroicon-o-calendar-days class="h-5 w-5 text-violet-600 dark:text-violet-400" />
            </div>
            <p class="text-2xl font-bold {{ $quickStats['attendance_color'] }}">
                {{ $quickStats['attendance_label'] }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Attendance</p>
            @if ($quickStats['attendance_pct'] > 0)
                <div class="w-full max-w-[120px] overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800" style="height:4px;">
                    <div
                        class="h-full rounded-full {{ $quickStats['attendance_pct'] >= 75 ? 'bg-green-500' : ($quickStats['attendance_pct'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                        style="width: {{ min(100, $quickStats['attendance_pct']) }}%"
                    ></div>
                </div>
            @endif
        </div>

        {{-- Latest Grade --}}
        <div class="flex flex-col items-center justify-center gap-2 px-6 py-5 text-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                <x-heroicon-o-trophy class="h-5 w-5 text-blue-600 dark:text-blue-400" />
            </div>
            <span class="inline-flex items-center rounded-lg px-3 py-1.5 text-xl font-bold {{ $quickStats['grade_class'] }}">
                {{ $quickStats['latest_grade'] }}
            </span>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Latest Grade
                @if ($quickStats['latest_exam'] !== '—')
                    <span class="block text-gray-400 dark:text-gray-600">{{ $quickStats['latest_exam'] }}</span>
                @endif
            </p>
        </div>

        {{-- Fee Status --}}
        <div class="flex flex-col items-center justify-center gap-2 px-6 py-5 text-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30">
                <x-heroicon-o-banknotes class="h-5 w-5 text-amber-600 dark:text-amber-400" />
            </div>
            <p class="text-lg font-bold {{ $quickStats['fee_color'] }}">
                {{ $quickStats['fee_label'] }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Fee Status</p>
        </div>

    </div>
</div>

{{-- ========================================================================== --}}
{{-- SUBJECTS                                                                     --}}
{{-- ========================================================================== --}}
<div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
    <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
            <x-heroicon-o-book-open class="h-4 w-4 text-purple-600 dark:text-purple-400" />
        </div>
        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">My Subjects</h2>
        @if (count($subjects) > 0)
            <span class="ml-auto rounded-full bg-purple-100 px-2 py-0.5 text-xs font-semibold text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                {{ count($subjects) }}
            </span>
        @endif
    </div>

    <div class="px-5 py-4">
        @if (count($subjects) === 0)
            <div class="flex flex-col items-center gap-2 py-8 text-center">
                <x-heroicon-o-book-open class="h-10 w-10 text-gray-300 dark:text-gray-600" />
                <p class="text-sm text-gray-400 dark:text-gray-500">No subjects assigned to your class yet.</p>
            </div>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach ($subjects as $subject)
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold {{ $subject['badge_class'] }}">
                        {{ $subject['name'] }}
                        @if ($subject['code'])
                            <span class="opacity-60">({{ $subject['code'] }})</span>
                        @endif
                    </span>
                @endforeach
            </div>

            {{-- Legend --}}
            <div class="mt-4 flex flex-wrap gap-3 border-t border-gray-50 pt-3 dark:border-gray-800">
                <span class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                    <span class="h-2 w-2 rounded-full bg-blue-400"></span> Theory
                </span>
                <span class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                    <span class="h-2 w-2 rounded-full bg-green-400"></span> Practical
                </span>
                <span class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                    <span class="h-2 w-2 rounded-full bg-purple-400"></span> Elective
                </span>
                <span class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                    <span class="h-2 w-2 rounded-full bg-orange-400"></span> Project
                </span>
            </div>
        @endif
    </div>
</div>

{{-- ========================================================================== --}}
{{-- MY TEACHERS                                                                  --}}
{{-- ========================================================================== --}}
<div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
    <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-100 dark:bg-rose-900/30">
            <x-heroicon-o-users class="h-4 w-4 text-rose-600 dark:text-rose-400" />
        </div>
        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">My Teachers</h2>
        @if (count($teachers) > 0)
            <span class="ml-auto rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">
                {{ count($teachers) }}
            </span>
        @endif
    </div>

    @if (count($teachers) === 0)
        <div class="flex flex-col items-center gap-2 py-10 text-center">
            <x-heroicon-o-users class="h-10 w-10 text-gray-300 dark:text-gray-600" />
            <p class="text-sm text-gray-400 dark:text-gray-500">No teacher assignments found for your class.</p>
        </div>
    @else
        <div class="divide-y divide-gray-50 dark:divide-gray-800">
            @foreach ($teachers as $teacher)
                <div class="flex items-center gap-4 px-5 py-3.5">
                    {{-- Avatar initial --}}
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-violet-500 to-purple-600 shadow-sm">
                        <span class="text-sm font-bold text-white">
                            {{ strtoupper(substr($teacher['faculty_name'], 0, 1)) }}
                        </span>
                    </div>

                    {{-- Name + subject --}}
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ $teacher['faculty_name'] }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $teacher['subject'] }}</p>
                    </div>

                    {{-- Subject type badge --}}
                    @if ($teacher['subject_type'])
                        <span class="flex-shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $teacher['badge_class'] }}">
                            {{ ucfirst($teacher['subject_type']) }}
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

@endif
{{-- end hasProfile --}}

</x-filament-panels::page>

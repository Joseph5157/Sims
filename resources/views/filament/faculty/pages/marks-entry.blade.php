<x-filament-panels::page>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;600&family=Lora:wght@500;600;700&display=swap');

.me-ui *, .me-ui *::before, .me-ui *::after { box-sizing: border-box; }

/* ══ INFO BAR ════════════════════════════════════════════ */
.me-info-bar {
    background: #1a1a2e;
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 8px;
    display: flex; align-items: center; justify-content: space-between;
    border-bottom: 3px solid #c9a84c;
}
.me-ib-class  { color: #94a3b8; font-size: 10px; letter-spacing: 1px; font-family: 'DM Mono', monospace; margin-bottom: 3px; text-transform: uppercase; }
.me-ib-name   { color: #fff; font-size: 17px; font-weight: 700; font-family: 'Lora', serif; }
.me-ib-right  { text-align: right; }
.me-ib-badge  { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 800; font-family: 'DM Mono', monospace; margin-bottom: 3px; }
.me-ib-badge.fa { background: #1d4ed820; color: #3b82f6; border: 1px solid #3b82f660; }
.me-ib-badge.sa { background: #c9a84c20; color: #c9a84c; border: 1px solid #c9a84c60; }
.me-ib-sub    { color: #475569; font-size: 10px; margin-top: 1px; }

/* ══ STATS STRIP ═════════════════════════════════════════ */
.me-stats-strip {
    background: #1a1a2e;
    border-radius: 14px;
    padding: 12px 20px;
    margin-bottom: 8px;
    display: flex; gap: 0;
}
.me-stat { flex: 1; text-align: center; position: relative; }
.me-stat + .me-stat::before {
    content: ''; position: absolute; left: 0; top: 20%;
    height: 60%; width: 1px; background: #2d2d4a;
}
.me-stat-num { font-size: 18px; font-weight: 800; font-family: 'DM Mono', monospace; line-height: 1; }
.me-stat-lbl { font-size: 9px; font-weight: 600; margin-top: 3px; letter-spacing: .5px; color: #64748b; }
.me-stat-avg  .me-stat-num { color: #c9a84c; }
.me-stat-high .me-stat-num { color: #22c55e; }
.me-stat-low  .me-stat-num { color: #ef4444; }
.me-stat-done .me-stat-num { color: #3b82f6; }

/* ══ PROGRESS BAR ════════════════════════════════════════ */
.me-prog-wrap { margin-bottom: 8px; }
.me-prog-meta { display: flex; justify-content: space-between; margin-bottom: 5px; }
.me-prog-label { font-size: 11px; font-family: 'DM Mono', monospace; font-weight: 700; color: #64748b; }
.me-prog-label.done { color: #16a34a; }
.me-prog-track { height: 4px; background: #e2e8f0; border-radius: 4px; overflow: hidden; }
.dark .me-prog-track { background: #1e293b; }
.me-prog-fill { height: 100%; background: #c9a84c; border-radius: 4px; transition: width .4s ease; }
.me-prog-fill.done { background: #22c55e; }

/* ══ TABLE WRAPPER ════════════════════════════════════════ */
.me-table-wrap {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    border: 1.5px solid #e8e0d0;
    margin-bottom: 10px;
}
.dark .me-table-wrap { background: #1e293b; border-color: #334155; }

/* ══ TABLE HEADER ════════════════════════════════════════ */
.me-table-head {
    background: #1a1a2e;
    display: grid;
    gap: 0;
    padding: 10px 14px;
    border-bottom: 2px solid #c9a84c;
}
.me-th {
    font-size: 9px; font-weight: 700; font-family: 'DM Mono', monospace;
    color: #94a3b8; letter-spacing: .8px; text-align: center;
}
.me-th.name-col { text-align: left; }

/* ══ STUDENT ROW ═════════════════════════════════════════ */
.me-s-row {
    display: grid;
    gap: 0;
    padding: 0 14px;
    align-items: center;
    border-bottom: 1px solid #f5f0e6;
    min-height: 58px;
    transition: background .15s;
}
.dark .me-s-row { border-bottom-color: #2d2d4a; }
.me-s-row:last-child { border-bottom: none; }
.me-s-row:nth-child(even) { background: #fafaf5; }
.dark .me-s-row:nth-child(even) { background: #1a2535; }
.me-s-row.absent-row { opacity: .6; background: #fafafa !important; }

/* Roll */
.me-s-roll {
    font-size: 10px; font-family: 'DM Mono', monospace;
    color: #94a3b8; font-weight: 600; text-align: center;
}

/* Name */
.me-s-name-wrap { min-width: 0; }
.me-s-name {
    font-size: 13px; font-weight: 600; font-family: 'Lora', serif;
    color: #1a1a2e; white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis;
}
.dark .me-s-name { color: #f1f5f9; }

/* Progress bar under name */
.me-s-bar-track {
    height: 3px; background: #f1f5f9; border-radius: 3px;
    overflow: hidden; margin-top: 4px;
    transition: all .3s;
}
.dark .me-s-bar-track { background: #2d2d4a; }
.me-s-bar-fill {
    height: 100%; border-radius: 3px;
    transition: width .35s ease, background .35s;
}

/* Absent toggle cell */
.me-abs-cell { display: flex; align-items: center; justify-content: center; }

/* Mark input */
.me-input {
    width: 100%; border-radius: 8px; border: 1.5px solid #e2e8f0;
    padding: 7px 6px; text-align: center;
    font-size: 14px; font-weight: 800; font-family: 'DM Mono', monospace;
    outline: none; transition: all .2s;
    background: #fafafa; color: #1a1a2e;
    -moz-appearance: textfield;
}
.me-input::-webkit-outer-spin-button,
.me-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.me-input:focus { border-color: #c9a84c; box-shadow: 0 0 0 3px rgba(201,168,76,.15); }
.me-input.disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; border-color: #e2e8f0; }
.dark .me-input { background: #0f172a; border-color: #334155; color: #f1f5f9; }
.dark .me-input.disabled { background: #1e293b; color: #475569; }

/* Color-coded input states */
.me-input.grade-a  { background: #f0fdf4; border-color: #86efac; color: #15803d; }
.me-input.grade-b  { background: #eff6ff; border-color: #93c5fd; color: #1d4ed8; }
.me-input.grade-c  { background: #fffbeb; border-color: #fde68a; color: #b45309; }
.me-input.grade-d  { background: #fff7ed; border-color: #fed7aa; color: #c2410c; }
.me-input.grade-f  { background: #fef2f2; border-color: #fca5a5; color: #dc2626; }
.dark .me-input.grade-a { background: #052e16; border-color: #166534; color: #4ade80; }
.dark .me-input.grade-b { background: #0f1e3d; border-color: #1e40af; color: #93c5fd; }
.dark .me-input.grade-c { background: #422006; border-color: #92400e; color: #fde68a; }
.dark .me-input.grade-d { background: #431407; border-color: #9a3412; color: #fed7aa; }
.dark .me-input.grade-f { background: #450a0a; border-color: #991b1b; color: #fca5a5; }

/* Grade badge */
.me-grade {
    display: flex; align-items: center; justify-content: center;
}
.me-grade-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 32px; height: 28px; border-radius: 8px;
    font-size: 11px; font-weight: 800; font-family: 'DM Mono', monospace;
    border: 1.5px solid; transition: all .2s;
}
.me-grade-badge.A1,.me-grade-badge.A2 { background:#f0fdf4;border-color:#86efac;color:#16a34a; }
.me-grade-badge.B1,.me-grade-badge.B2 { background:#eff6ff;border-color:#93c5fd;color:#1d4ed8; }
.me-grade-badge.C1,.me-grade-badge.C2 { background:#fffbeb;border-color:#fde68a;color:#b45309; }
.me-grade-badge.D1,.me-grade-badge.D2 { background:#fff7ed;border-color:#fed7aa;color:#c2410c; }
.me-grade-badge.E  { background:#fef2f2;border-color:#fca5a5;color:#dc2626; }
.me-grade-badge.AB { background:#faf5ff;border-color:#c4b5fd;color:#7c3aed; }
.me-grade-badge.em { background:#f1f5f9;border-color:#e2e8f0;color:#94a3b8; }
.dark .me-grade-badge.A1,.dark .me-grade-badge.A2 { background:#052e16;border-color:#166534;color:#4ade80; }
.dark .me-grade-badge.B1,.dark .me-grade-badge.B2 { background:#0f1e3d;border-color:#1e40af;color:#93c5fd; }
.dark .me-grade-badge.C1,.dark .me-grade-badge.C2 { background:#422006;border-color:#92400e;color:#fde68a; }
.dark .me-grade-badge.D1,.dark .me-grade-badge.D2 { background:#431407;border-color:#9a3412;color:#fed7aa; }
.dark .me-grade-badge.E  { background:#450a0a;border-color:#991b1b;color:#fca5a5; }
.dark .me-grade-badge.AB { background:#2e1065;border-color:#6d28d9;color:#ddd6fe; }
.dark .me-grade-badge.em { background:#1e293b;border-color:#334155;color:#475569; }

/* Total badge */
.me-total-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 38px; height: 28px; border-radius: 8px;
    font-size: 13px; font-weight: 800; font-family: 'DM Mono', monospace;
    background: #f1f5f9; color: #475569; border: 1.5px solid #e2e8f0;
    transition: all .2s;
}
.dark .me-total-badge { background: #0f172a; border-color: #334155; color: #94a3b8; }

/* Footer row */
.me-footer {
    background: #1a1a2e;
    padding: 10px 14px;
    display: flex; align-items: center; gap: 16px;
    border-top: 2px solid #c9a84c;
}
.me-footer-item { font-size: 11px; font-family: 'DM Mono', monospace; font-weight: 600; }

/* ══ WRITING LANG SECTION ════════════════════════════════ */
.me-lang-section {
    background: #fff;
    border-radius: 12px;
    border: 1.5px solid #e8e0d0;
    overflow: hidden;
    margin-bottom: 10px;
}
.dark .me-lang-section { background: #1e293b; border-color: #334155; }
.me-lang-head {
    background: #1a1a2e;
    padding: 9px 14px;
    font-size: 10px; font-weight: 700; font-family: 'DM Mono', monospace;
    color: #94a3b8; letter-spacing: .8px;
    display: flex; align-items: center; justify-content: space-between;
}
.me-lang-row {
    display: flex; align-items: center; gap: 12px;
    padding: 8px 14px; border-bottom: 1px solid #f5f0e6;
}
.dark .me-lang-row { border-bottom-color: #2d2d4a; }
.me-lang-row:last-child { border-bottom: none; }
.me-lang-name { font-size: 12px; font-weight: 600; font-family: 'Lora', serif; color: #1a1a2e; flex: 1; }
.dark .me-lang-name { color: #f1f5f9; }
.me-lang-input {
    width: 140px; padding: 6px 10px; border-radius: 8px;
    border: 1.5px solid #e2e8f0; font-size: 12px;
    font-family: 'DM Sans', sans-serif; outline: none;
    background: #fafafa; color: #1a1a2e;
    transition: border-color .2s;
}
.dark .me-lang-input { background: #0f172a; border-color: #334155; color: #f1f5f9; }
.me-lang-input:focus { border-color: #c9a84c; }

/* ══ SAVE BAR ════════════════════════════════════════════ */
.me-save-bar {
    position: sticky; bottom: 0;
    background: #fff;
    border-top: 2px solid #e8e0d0;
    padding: 12px 0 16px;
    margin-top: 12px;
    display: flex; align-items: center; gap: 12px; z-index: 50;
}
.dark .me-save-bar { background: #0f172a; border-top-color: #1e293b; }
.me-sb-info { flex: 1; font-size: 12px; font-weight: 600; color: #64748b; font-family: 'DM Sans', sans-serif; }
.me-sb-info.done { color: #16a34a; font-weight: 800; }
.me-sb-btn {
    padding: 13px 26px; border-radius: 11px;
    background: #1a1a2e; color: #c9a84c; border: none;
    font-size: 13px; font-weight: 800; font-family: 'DM Sans', sans-serif;
    cursor: pointer; box-shadow: 0 4px 14px rgba(26,26,46,.25);
    letter-spacing: .3px; transition: opacity .2s, transform .1s;
}
.me-sb-btn:disabled { opacity: .6; cursor: not-allowed; }
.me-sb-btn:active:not(:disabled) { transform: scale(.97); }
</style>

<div class="me-ui">

    {{-- ── CONFIG ────────────────────────────────────────── --}}
    <div class="mb-4">
        <x-filament::section>
            <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">

                {{-- Exam Group --}}
                <div class="flex-1 min-w-[220px]">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Exam Group
                    </label>
                    <select
                        wire:model.live="examGroupId"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    >
                        <option value="">— Select exam group —</option>
                        @foreach ($availableExamGroups as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Subject --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Subject
                    </label>
                    <select
                        wire:model.live="subjectId"
                        @disabled(!$examGroupId)
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    >
                        <option value="">— Select subject —</option>
                        @foreach ($availableSubjects as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @if ($examGroupId && $subjectId === null && empty($availableSubjects))
                        <p class="mt-1 text-xs text-warning-600 dark:text-warning-400">No exams set up for this group. Ask admin to create exams first.</p>
                    @endif
                </div>

            </div>
        </x-filament::section>
    </div>

    @if ($examGroupId && $subjectId && empty($exams))
        <x-filament::section>
            <div class="flex items-center gap-3 py-4 text-warning-600 dark:text-warning-400">
                <x-heroicon-o-exclamation-triangle class="h-5 w-5 flex-shrink-0" />
                <p class="text-sm">No exam records found for this group + subject. Ask the admin to create the exam records first.</p>
            </div>
        </x-filament::section>
    @endif

    @if (!empty($students) && !empty($exams))

    {{-- ── COMPUTED ──────────────────────────────────────── --}}
    @php
        $isFa = ($examGroupType === 'fa');
        $maxTotal = array_sum(array_column($exams, 'maximum_marks'));

        // Summary stats
        $allMarks = [];
        $filledCount = 0;
        $absentCount = 0;
        foreach ($students as $s) {
            $sid = $s['id'];
            if ($absent[$sid] ?? false) { $absentCount++; continue; }
            $hasAny = false;
            foreach ($exams as $exam) {
                $v = $marks[$sid][$exam['id']] ?? '';
                if ($v !== '' && $v !== null) { $allMarks[] = (float)$v; $hasAny = true; }
            }
            if ($hasAny) $filledCount++;
        }
        $avgMark = count($allMarks) ? round(array_sum($allMarks)/count($allMarks), 1) : null;
        $highMark = count($allMarks) ? max($allMarks) : null;
        $lowMark  = count($allMarks) ? min($allMarks) : null;
        $totalStudents = count($students);
        $progPct = $totalStudents > 0 ? round(($filledCount / ($totalStudents - $absentCount ?: 1)) * 100) : 0;
        $progDone = $filledCount >= ($totalStudents - $absentCount);

        // Subject name
        $subjectName = $availableSubjects[$subjectId] ?? 'Subject';
        $className   = $facultyClasses[$examGroupClassId ?? 0] ?? '';

        // Grade class helper
        $gradeCssClass = function(?string $g): string {
            if (!$g || $g === '—') return 'em';
            return $g;
        };

        // Input color class based on percentage
        $inputClass = function($val, $max) {
            if ($val === '' || $val === null || $max == 0) return '';
            $p = ((float)$val / (float)$max) * 100;
            if ($p >= 80) return 'grade-a';
            if ($p >= 60) return 'grade-b';
            if ($p >= 40) return 'grade-c';
            if ($p >= 25) return 'grade-d';
            return 'grade-f';
        };

        // Bar color
        $barColor = function(float $pct): string {
            if ($pct >= 80) return '#22c55e';
            if ($pct >= 60) return '#3b82f6';
            if ($pct >= 40) return '#f59e0b';
            return '#ef4444';
        };
    @endphp

    {{-- ── INFO BAR ──────────────────────────────────────── --}}
    <div class="me-info-bar">
        <div>
            <div class="me-ib-class">{{ strtoupper($className) }}</div>
            <div class="me-ib-name">Marks Register</div>
        </div>
        <div class="me-ib-right">
            @if ($examGroupType)
                <div class="me-ib-badge {{ $examGroupType }}">
                    {{ $examGroupType === 'fa' ? 'Formative (FA)' : 'Summative (SA)' }}
                </div>
            @endif
            <div class="me-ib-sub">{{ $subjectName }}</div>
        </div>
    </div>

    {{-- ── STATS STRIP ────────────────────────────────────── --}}
    <div class="me-stats-strip">
        <div class="me-stat me-stat-avg">
            <div class="me-stat-num">{{ $avgMark ?? '—' }}</div>
            <div class="me-stat-lbl">AVG</div>
        </div>
        <div class="me-stat me-stat-high">
            <div class="me-stat-num">{{ $highMark ?? '—' }}</div>
            <div class="me-stat-lbl">HIGHEST</div>
        </div>
        <div class="me-stat me-stat-low">
            <div class="me-stat-num">{{ $lowMark ?? '—' }}</div>
            <div class="me-stat-lbl">LOWEST</div>
        </div>
        <div class="me-stat me-stat-done">
            <div class="me-stat-num">{{ $filledCount }}/{{ $totalStudents - $absentCount }}</div>
            <div class="me-stat-lbl">ENTERED</div>
        </div>
    </div>

    {{-- ── PROGRESS ─────────────────────────────────────── --}}
    <div class="me-prog-wrap">
        <div class="me-prog-meta">
            <span class="me-prog-label {{ $progDone ? 'done' : '' }}">
                @if ($progDone)
                    ✓ All marks entered
                @else
                    {{ ($totalStudents - $absentCount) - $filledCount }} student{{ (($totalStudents - $absentCount) - $filledCount) !== 1 ? 's' : '' }} remaining
                @endif
            </span>
            <span style="font-size:11px;font-family:'DM Mono',monospace;font-weight:700;color:#c9a84c">{{ $progPct }}%</span>
        </div>
        <div class="me-prog-track">
            <div class="me-prog-fill {{ $progDone ? 'done' : '' }}" style="width:{{ $progPct }}%"></div>
        </div>
    </div>

    {{-- ── MARKS TABLE ─────────────────────────────────── --}}
    @php
        // Grid columns: roll | name | absent | [exams...] | [total if FA] | grade
        $examCount = count($exams);
        $gridCols = "38px minmax(120px,1fr) 48px " . str_repeat("minmax(70px,1fr) ", $examCount) . ($isFa ? "60px " : "") . "52px";
    @endphp

    <div class="me-table-wrap">
        {{-- Header --}}
        <div class="me-table-head" style="grid-template-columns:{{ $gridCols }}">
            <span class="me-th">NO.</span>
            <span class="me-th name-col">STUDENT</span>
            <span class="me-th">ABS</span>
            @foreach ($exams as $exam)
                <span class="me-th">
                    {{ $exam['label'] }}<br>
                    <span style="color:#64748b;font-weight:400;font-size:8px">/ {{ (int)$exam['maximum_marks'] }}</span>
                </span>
            @endforeach
            @if ($isFa)
                <span class="me-th">TOTAL<br><span style="color:#64748b;font-weight:400;font-size:8px">/ {{ (int)$maxTotal }}</span></span>
            @endif
            <span class="me-th">GRD</span>
        </div>

        {{-- Rows --}}
        @foreach ($students as $student)
            @php
                $sid     = $student['id'];
                $isAbs   = (bool)($absent[$sid] ?? false);
                $grade   = $grades[$sid] ?? '—';
                $total   = $totals[$sid] ?? 0;
                $gradeCss = $gradeCssClass($grade);

                // Per-student bar
                $barPct = 0;
                if (!$isAbs && $maxTotal > 0) {
                    $barPct = min(100, round(($total / $maxTotal) * 100));
                }
            @endphp

            <div class="me-s-row {{ $isAbs ? 'absent-row' : '' }}"
                 style="grid-template-columns:{{ $gridCols }}">

                {{-- Roll --}}
                <div class="me-s-roll">{{ $student['roll_number'] }}</div>

                {{-- Name + bar --}}
                <div class="me-s-name-wrap">
                    <div class="me-s-name">{{ $student['name'] }}</div>
                    @if (!$isAbs)
                        <div class="me-s-bar-track">
                            <div class="me-s-bar-fill"
                                 style="width:{{ $barPct }}%;background:{{ $barColor($barPct) }}"></div>
                        </div>
                    @endif
                </div>

                {{-- Absent toggle --}}
                <div class="me-abs-cell">
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox"
                               wire:model.live="absent.{{ $sid }}"
                               class="peer sr-only" />
                        <div class="peer h-5 w-9 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-red-500 peer-checked:after:translate-x-full peer-focus:outline-none dark:bg-gray-600"></div>
                    </label>
                </div>

                {{-- Marks inputs --}}
                @foreach ($exams as $exam)
                    @php
                        $eid = $exam['id'];
                        $max = $exam['maximum_marks'];
                        $val = $marks[$sid][$eid] ?? '';
                        $ic  = $isAbs ? '' : $inputClass($val, $max);
                    @endphp
                    <div style="padding:6px 4px">
                        <input
                            type="number"
                            wire:model.live.debounce.400ms="marks.{{ $sid }}.{{ $eid }}"
                            min="0"
                            max="{{ $max }}"
                            step="0.5"
                            placeholder="—"
                            @disabled($isAbs)
                            class="me-input {{ $isAbs ? 'disabled' : $ic }}"
                        />
                    </div>
                @endforeach

                {{-- FA Total --}}
                @if ($isFa)
                    <div style="display:flex;align-items:center;justify-content:center;padding:6px 4px">
                        @if ($isAbs)
                            <span class="me-total-badge" style="color:#94a3b8">—</span>
                        @else
                            <span class="me-total-badge" style="color:#1a1a2e;background:#f8f7f4;border-color:#e8e0d0">
                                {{ $total == floor($total) ? (int)$total : number_format($total, 1) }}
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Grade --}}
                <div class="me-grade" style="padding:6px 4px">
                    <div class="me-grade-badge {{ $gradeCss }}">
                        {{ $grade === '—' ? '—' : $grade }}
                    </div>
                </div>

            </div>
        @endforeach

        {{-- Footer --}}
        @php
            $footPresent = count(array_filter(array_values($absent), fn($v) => !$v));
            $footAbsent  = count(array_filter(array_values($absent), fn($v) => (bool)$v));
        @endphp
        <div class="me-footer">
            <span class="me-footer-item" style="color:#22c55e">✓ {{ $footPresent }} present</span>
            <span class="me-footer-item" style="color:#ef4444">— {{ $footAbsent }} absent</span>
            <span class="me-footer-item" style="color:#94a3b8;margin-left:auto">{{ $totalStudents }} total</span>
        </div>
    </div>

    {{-- ── WRITING LANGUAGE (collapsed section, not inline) ── --}}
    <details class="me-lang-section" style="margin-bottom:10px">
        <summary style="cursor:pointer;padding:10px 14px;font-size:10px;font-weight:700;font-family:'DM Mono',monospace;color:#94a3b8;letter-spacing:.8px;background:#1a1a2e;list-style:none;display:flex;align-items:center;justify-content:space-between">
            <span>WRITING LANGUAGE <span style="color:#475569;font-weight:400">(optional)</span></span>
            <span style="font-size:12px;color:#c9a84c">▾</span>
        </summary>
        @foreach ($students as $student)
            @php $sid = $student['id']; @endphp
            <div class="me-lang-row">
                <div class="me-lang-name">{{ $student['name'] }}</div>
                <input
                    type="text"
                    wire:model.lazy="writingLanguage.{{ $sid }}"
                    placeholder="e.g. English"
                    maxlength="100"
                    class="me-lang-input"
                />
            </div>
        @endforeach
    </details>

    {{-- ── SAVE BAR ──────────────────────────────────────── --}}
    <div class="me-save-bar">
        <div class="me-sb-info {{ $progDone ? 'done' : '' }}">
            @if ($progDone)
                ✓ All marks entered — ready to save
            @else
                {{ $filledCount }}/{{ $totalStudents - $absentCount }} marks entered
            @endif
        </div>
        <button
            wire:click="save"
            wire:loading.attr="disabled"
            class="me-sb-btn"
        >
            <span wire:loading.remove wire:target="save">Save Marks →</span>
            <span wire:loading wire:target="save">Saving…</span>
        </button>
    </div>

    @elseif (!$examGroupId)
        <x-filament::section>
            <div class="flex flex-col items-center gap-3 py-10 text-gray-400 dark:text-gray-500">
                <x-heroicon-o-pencil-square class="h-10 w-10" />
                <p class="text-sm">Select an exam group and subject to begin entering marks.</p>
            </div>
        </x-filament::section>
    @endif

</div>

</x-filament-panels::page>

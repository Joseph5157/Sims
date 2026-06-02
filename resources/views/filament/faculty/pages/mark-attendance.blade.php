<x-filament-panels::page>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;600&family=Lora:wght@500;600;700&display=swap');

.att-ui *, .att-ui *::before, .att-ui *::after { box-sizing: border-box; }

/* ══ INFO BAR ══════════════════════════════════════════════ */
.att-info-bar {
    background: #1a1a2e;
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 3px solid #c9a84c;
}
.att-ib-class  { color: #94a3b8; font-size: 10px; letter-spacing: 1px; font-family: 'DM Mono', monospace; margin-bottom: 3px; text-transform: uppercase; }
.att-ib-name   { color: #fff; font-size: 17px; font-weight: 700; font-family: 'Lora', serif; }
.att-ib-right  { text-align: right; }
.att-ib-date   { color: #c9a84c; font-size: 10px; font-family: 'DM Mono', monospace; font-weight: 600; }
.att-ib-teacher{ color: #475569; font-size: 10px; margin-top: 2px; }

/* ══ STATS STRIP ════════════════════════════════════════════ */
.att-stats-strip {
    background: #1a1a2e;
    border-radius: 14px;
    padding: 12px 20px;
    margin-bottom: 8px;
    display: flex;
    gap: 0;
}
.att-stat { flex: 1; text-align: center; position: relative; }
.att-stat + .att-stat::before {
    content: '';
    position: absolute; left: 0; top: 20%; height: 60%; width: 1px;
    background: #2d2d4a;
}
.att-stat-num { font-size: 20px; font-weight: 800; font-family: 'DM Mono', monospace; line-height: 1; }
.att-stat-lbl { font-size: 9px; font-weight: 600; margin-top: 3px; letter-spacing: .5px; }
.att-stat-p .att-stat-num { color: #22c55e; } .att-stat-p .att-stat-lbl { color: #16a34a; }
.att-stat-a .att-stat-num { color: #ef4444; } .att-stat-a .att-stat-lbl { color: #dc2626; }
.att-stat-l .att-stat-num { color: #f59e0b; } .att-stat-l .att-stat-lbl { color: #d97706; }
.att-stat-e .att-stat-num { color: #a855f7; } .att-stat-e .att-stat-lbl { color: #9333ea; }

/* ══ PROGRESS BAR ═══════════════════════════════════════════ */
.att-prog-wrap { margin-bottom: 8px; }
.att-prog-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
.att-prog-label { font-size: 11px; font-family: 'DM Mono', monospace; font-weight: 700; color: #64748b; }
.att-prog-label.done { color: #16a34a; }
.att-prog-track { height: 4px; background: #e2e8f0; border-radius: 4px; overflow: hidden; }
.dark .att-prog-track { background: #1e293b; }
.att-prog-fill { height: 100%; background: #c9a84c; border-radius: 4px; transition: width .4s ease, background .4s; }
.att-prog-fill.done { background: #22c55e; }

/* ══ NOTICE ═════════════════════════════════════════════════ */
.att-notice {
    display: flex; align-items: center; gap: 10px;
    background: #1e3a5f; border-radius: 10px;
    padding: 10px 14px; margin-bottom: 8px;
    color: #93c5fd; font-size: 11px; font-weight: 600;
}

/* ══ CONTROLS ═══════════════════════════════════════════════ */
.att-ctrl-row { display: flex; gap: 8px; margin-bottom: 8px; align-items: center; }
.att-search-box {
    flex: 1; display: flex; align-items: center; gap: 8px;
    background: #fff; border: 1.5px solid #e2e8f0;
    border-radius: 10px; padding: 0 12px;
}
.dark .att-search-box { background: #1e293b; border-color: #334155; }
.att-search-box input {
    border: none; outline: none; font-size: 13px; color: #0f172a;
    background: transparent; width: 100%; padding: 10px 0;
    font-family: 'DM Sans', sans-serif;
}
.dark .att-search-box input { color: #f1f5f9; }
.att-search-icon { color: #94a3b8; font-size: 16px; flex-shrink: 0; }
.att-markall-wrap { position: relative; flex-shrink: 0; }
.att-markall-btn {
    padding: 10px 14px; border-radius: 10px;
    border: 1.5px solid #e2e8f0; background: #fff;
    font-size: 12px; font-weight: 700; cursor: pointer;
    color: #0f172a; font-family: 'DM Sans', sans-serif; white-space: nowrap;
}
.dark .att-markall-btn { background: #1e293b; border-color: #334155; color: #f1f5f9; }
.att-dropdown {
    position: absolute; right: 0; top: calc(100% + 6px);
    background: #1a1a2e; border-radius: 12px; overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,.3); z-index: 200; min-width: 164px;
    border: 1px solid #2d2d4a;
}
.att-dd-item {
    display: block; width: 100%; padding: 12px 16px;
    border: none; background: none; cursor: pointer;
    text-align: left; font-size: 13px; font-weight: 700;
    font-family: 'DM Sans', sans-serif;
    border-bottom: 1px solid #2d2d4a;
}
.att-dd-item:last-child { border-bottom: none; }
.att-dd-item.p { color: #22c55e; } .att-dd-item.a { color: #ef4444; }
.att-dd-item.l { color: #f59e0b; } .att-dd-item.e { color: #a855f7; }

/* ══ LEGEND ════════════════════════════════════════════════ */
.att-legend {
    display: flex; gap: 12px; align-items: center;
    padding: 6px 0; margin-bottom: 10px; flex-wrap: wrap;
}
.att-leg-item { display: flex; align-items: center; gap: 5px; font-size: 10px; color: #94a3b8; font-weight: 600; font-family: 'DM Mono', monospace; }
.att-leg-sym {
    width: 22px; height: 22px; border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 900; border: 1.5px solid;
}
.att-leg-hint { margin-left: auto; font-size: 9px; color: #64748b; font-family: 'DM Mono', monospace; font-weight: 600; }

/* ══ SECTION HEADERS ════════════════════════════════════════ */
.att-section-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 6px 2px; margin-bottom: 6px; margin-top: 4px;
}
.att-section-title { font-size: 10px; font-weight: 700; color: #94a3b8; letter-spacing: 1px; font-family: 'DM Mono', monospace; }
.att-section-count { font-size: 10px; font-weight: 700; font-family: 'DM Mono', monospace; }

/* ══ GROUP DIVIDERS ═════════════════════════════════════════ */
.att-group-div {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 2px 4px; margin-top: 4px;
}
.att-group-line { flex: 1; height: 1px; background: #e8e0d0; }
.dark .att-group-line { background: #2d2d4a; }
.att-group-label { font-size: 9px; font-family: 'DM Mono', monospace; color: #b8b0a0; font-weight: 700; white-space: nowrap; }

/* ══ REGISTER TABLE ═════════════════════════════════════════ */
.att-reg-table-wrap {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    border: 1.5px solid #e8e0d0;
    margin-bottom: 10px;
}
.dark .att-reg-table-wrap { background: #1e293b; border-color: #334155; }

.att-reg-head {
    display: grid;
    grid-template-columns: 38px 1fr 60px;
    background: #1a1a2e;
    padding: 10px 14px;
    gap: 8px;
}
.att-reg-head span {
    font-size: 9px; font-weight: 700; font-family: 'DM Mono', monospace;
    color: #94a3b8; letter-spacing: .8px;
}
.att-reg-head span:last-child { text-align: center; }

/* ══ STUDENT ROW (merged C+D) ════════════════════════════════ */
.att-s-row {
    display: grid;
    grid-template-columns: 38px 1fr 60px;
    padding: 0 14px;
    gap: 8px;
    align-items: center;
    border-bottom: 1px solid #f5f0e6;
    cursor: pointer;
    transition: background .15s, border-left-color .2s;
    min-height: 54px;
    position: relative;
    user-select: none;
    border-left: 3px solid transparent;
}
.dark .att-s-row { border-bottom-color: #2d2d4a; }
.att-s-row:last-child { border-bottom: none; }
.att-s-row:active { filter: brightness(.97); }

/* Row state colors */
.att-s-row.present  { background: #fff; }
.att-s-row.absent   { background: #fff8f8; border-left-color: #ef4444; }
.att-s-row.late     { background: #fffdf0; border-left-color: #f59e0b; }
.att-s-row.excused  { background: #faf5ff; border-left-color: #a855f7; }
.dark .att-s-row.present  { background: #1e293b; }
.dark .att-s-row.absent   { background: #2d1515; border-left-color: #ef4444; }
.dark .att-s-row.late     { background: #2d2510; border-left-color: #f59e0b; }
.dark .att-s-row.excused  { background: #1e1530; border-left-color: #a855f7; }

/* Alternating stripe for present rows */
.att-s-row:nth-child(even).present { background: #fafaf5; }
.dark .att-s-row:nth-child(even).present { background: #1a2535; }

/* Roll */
.att-s-roll {
    font-size: 10px; font-family: 'DM Mono', monospace;
    color: #94a3b8; font-weight: 600; text-align: center;
}

/* Name area */
.att-s-name-wrap { min-width: 0; }
.att-s-name {
    font-size: 13px; font-weight: 600;
    font-family: 'Lora', serif;
    color: #1a1a2e;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    transition: color .15s;
}
.dark .att-s-name { color: #f1f5f9; }
.att-s-row.absent  .att-s-name { color: #dc2626; }
.att-s-row.late    .att-s-name { color: #b45309; }
.att-s-row.excused .att-s-name { color: #7c3aed; }
.dark .att-s-row.absent  .att-s-name { color: #fca5a5; }
.dark .att-s-row.late    .att-s-name { color: #fde68a; }
.dark .att-s-row.excused .att-s-name { color: #ddd6fe; }

.att-s-sub {
    font-size: 9px; font-family: 'DM Mono', monospace;
    color: #94a3b8; margin-top: 2px; font-weight: 600;
}
.att-s-row.absent  .att-s-sub { color: #fca5a5; }
.att-s-row.late    .att-s-sub { color: #fbbf24; }
.att-s-row.excused .att-s-sub { color: #c4b5fd; }

/* ══ MARK CELL (the register tick) ══════════════════════════ */
.att-mark-wrap { display: flex; align-items: center; justify-content: center; }
.att-mark {
    width: 38px; height: 38px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 900; transition: all .18s;
    border: 2px solid; flex-shrink: 0;
}
/* Present ✓ */
.att-mark.present {
    background: #f0fdf4; border-color: #86efac;
    color: #16a34a; font-size: 18px;
}
/* Absent — */
.att-mark.absent {
    background: #fef2f2; border-color: #fca5a5;
    color: #dc2626; font-size: 20px; letter-spacing: -1px;
}
/* Late L */
.att-mark.late {
    background: #fffbeb; border-color: #fde68a;
    color: #d97706; font-size: 12px;
    font-family: 'DM Mono', monospace; letter-spacing: .5px;
}
/* Excused E */
.att-mark.excused {
    background: #f5f3ff; border-color: #c4b5fd;
    color: #7c3aed; font-size: 12px;
    font-family: 'DM Mono', monospace; letter-spacing: .5px;
}
/* Unmarked */
.att-mark.none {
    background: #f8f8f5; border-color: #e8e0d0;
    color: #d1c9bb; font-size: 11px;
    font-family: 'DM Mono', monospace;
}
.dark .att-mark.present { background: #052e16; border-color: #166534; color: #4ade80; }
.dark .att-mark.absent  { background: #450a0a; border-color: #991b1b; color: #fca5a5; }
.dark .att-mark.late    { background: #422006; border-color: #92400e; color: #fde68a; }
.dark .att-mark.excused { background: #2e1065; border-color: #6d28d9; color: #ddd6fe; }
.dark .att-mark.none    { background: #1e293b; border-color: #334155; color: #475569; }

/* Pop animation on tap */
@keyframes att-mark-pop {
    0%   { transform: scale(.82); }
    55%  { transform: scale(1.14); }
    100% { transform: scale(1); }
}
.att-mark.popping { animation: att-mark-pop .22s ease; }

/* ══ EXCEPTIONS PANEL ═══════════════════════════════════════ */
.att-exc-panel {
    background: #fff;
    border: 1.5px solid #e8e0d0;
    border-radius: 12px;
    margin-bottom: 10px;
    overflow: hidden;
}
.dark .att-exc-panel { background: #1e293b; border-color: #334155; }
.att-exc-head {
    background: #1a1a2e;
    padding: 9px 14px;
    display: flex; align-items: center; justify-content: space-between;
}
.att-exc-title { font-size: 10px; font-weight: 700; font-family: 'DM Mono', monospace; color: #fff; letter-spacing: .5px; }
.att-exc-count { font-size: 10px; font-weight: 700; font-family: 'DM Mono', monospace; color: #ef4444; }
.att-exc-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 14px;
    border-bottom: 1px solid #f5f0e6;
}
.dark .att-exc-item { border-bottom-color: #2d2d4a; }
.att-exc-item:last-child { border-bottom: none; }
.att-exc-roll { font-size: 9px; font-family: 'DM Mono', monospace; color: #94a3b8; font-weight: 600; min-width: 22px; }
.att-exc-name { flex: 1; font-size: 12px; font-weight: 600; font-family: 'Lora', serif; color: #1a1a2e; }
.dark .att-exc-name { color: #f1f5f9; }
.att-exc-sym {
    width: 28px; height: 28px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 900; border: 1.5px solid; flex-shrink: 0;
}
.att-exc-sym.absent  { background: #fef2f2; border-color: #fca5a5; color: #dc2626; font-size: 15px; }
.att-exc-sym.late    { background: #fffbeb; border-color: #fde68a; color: #d97706; font-size: 10px; font-family: 'DM Mono', monospace; font-weight: 800; }
.att-exc-sym.excused { background: #f5f3ff; border-color: #c4b5fd; color: #7c3aed; font-size: 10px; font-family: 'DM Mono', monospace; font-weight: 800; }
.att-exc-undo {
    font-size: 12px; color: #94a3b8; cursor: pointer;
    padding: 4px 8px; border-radius: 6px;
    border: 1.5px solid #e2e8f0; background: #f8fafc;
    transition: all .15s; font-weight: 700; flex-shrink: 0;
}
.dark .att-exc-undo { background: #0f172a; border-color: #334155; }
.att-exc-undo:hover { background: #f1f5f9; color: #1a1a2e; }

/* ══ SAVE BAR ═══════════════════════════════════════════════ */
.att-save-bar {
    position: sticky; bottom: 0;
    background: #fff;
    border-top: 2px solid #e8e0d0;
    padding: 12px 0 16px;
    margin-top: 12px;
    display: flex; align-items: center; gap: 12px; z-index: 50;
}
.dark .att-save-bar { background: #0f172a; border-top-color: #1e293b; }
.att-sb-info { flex: 1; font-size: 12px; font-weight: 600; color: #64748b; font-family: 'DM Sans', sans-serif; }
.att-sb-info.done { color: #16a34a; font-weight: 800; }
.att-sb-btn {
    padding: 13px 26px; border-radius: 11px;
    background: #1a1a2e; color: #c9a84c; border: none;
    font-size: 13px; font-weight: 800; font-family: 'DM Sans', sans-serif;
    cursor: pointer; box-shadow: 0 4px 14px rgba(26,26,46,.25);
    letter-spacing: .3px; transition: opacity .2s, transform .1s;
}
.att-sb-btn:disabled { opacity: .6; cursor: not-allowed; }
.att-sb-btn:active:not(:disabled) { transform: scale(.97); }
</style>

<div class="att-ui" x-data="{ search: '', ddOpen: false }">

    {{-- ── CONFIG SECTION ─────────────────────────────────── --}}
    <div class="mb-4">
        <x-filament::section>
            <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
                <div class="flex min-w-[180px] flex-col gap-1">
                    <label class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Attendance Date
                    </label>
                    <input
                        type="date"
                        wire:model.live="attendanceDate"
                        max="{{ now()->toDateString() }}"
                        class="block rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    />
                </div>

                @if (count($facultyClasses) > 1)
                    <div class="flex min-w-[200px] flex-1 flex-col gap-1">
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Class
                        </label>
                        <select
                            wire:model.live="selectedClassId"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                        >
                            <option value="">— Choose a class —</option>
                            @foreach ($facultyClasses as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            @if ($attendanceDate && $attendanceDate !== now()->toDateString())
                <div class="mt-4 flex flex-col gap-1">
                    <label class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">
                        Reason for Backdating <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-start gap-2 rounded-lg border border-amber-300 bg-amber-50 p-3 dark:border-amber-600 dark:bg-amber-900/20">
                        <x-heroicon-o-exclamation-triangle class="mt-0.5 h-4 w-4 flex-shrink-0 text-amber-600 dark:text-amber-400" />
                        <div class="flex-1">
                            <p class="mb-2 text-xs text-amber-700 dark:text-amber-300">
                                You are marking attendance for a past date
                                ({{ \Carbon\Carbon::parse($attendanceDate)->format('d M Y') }}).
                                A reason is required.
                            </p>
                            <textarea
                                wire:model.live="editReason"
                                rows="2"
                                placeholder="e.g. Network outage prevented marking on time…"
                                class="block w-full rounded-lg border border-amber-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-400 dark:border-amber-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                            ></textarea>
                        </div>
                    </div>
                </div>
            @endif
        </x-filament::section>
    </div>

    @if (empty($facultyClasses))
        <x-filament::section>
            <div class="flex items-center gap-3 text-warning-600 dark:text-warning-400">
                <x-heroicon-o-exclamation-triangle class="h-5 w-5 flex-shrink-0" />
                <p class="text-sm">No classes assigned. Please contact the administrator to add your timetable.</p>
            </div>
        </x-filament::section>
    @endif

    @if ($selectedClassId)

    {{-- ── COMPUTED ──────────────────────────────────────── --}}
    @php
        $className   = $facultyClasses[$selectedClassId] ?? 'Class';
        $teacherName = auth()->user()->name ?? '';
        $total       = count($students);
        $cP = $this->getPresentCount();
        $cA = $this->getAbsentCount();
        $cL = $this->getLateCount();
        $cE = $this->getExcusedCount();
        $exceptions  = array_values(array_filter($students,
            fn($s) => in_array($attendance[$s['id']] ?? 'present', ['absent','late','excused'])
        ));
        $allPresent  = ($cA + $cL + $cE) === 0;
        $pct         = $total > 0 ? round(($cP / $total) * 100) : 0;

        // Status symbols for register tick
        $SYM = ['present' => '✓', 'absent' => '—', 'late' => 'L', 'excused' => 'E'];

        // Group students into chunks of 10
        $groups = array_chunk($students, 10);
    @endphp

    {{-- ── INFO BAR ─────────────────────────────────────── --}}
    <div class="att-info-bar">
        <div>
            <div class="att-ib-class">{{ strtoupper($className) }}</div>
            <div class="att-ib-name">Attendance Register</div>
        </div>
        <div class="att-ib-right">
            <div class="att-ib-date">{{ \Carbon\Carbon::parse($attendanceDate)->format('D, d M Y') }}</div>
            <div class="att-ib-teacher">{{ $teacherName }}</div>
        </div>
    </div>

    {{-- ── STATS STRIP ──────────────────────────────────── --}}
    <div class="att-stats-strip">
        <div class="att-stat att-stat-p">
            <div class="att-stat-num">{{ $cP }}</div>
            <div class="att-stat-lbl">PRESENT</div>
        </div>
        <div class="att-stat att-stat-a">
            <div class="att-stat-num">{{ $cA }}</div>
            <div class="att-stat-lbl">ABSENT</div>
        </div>
        <div class="att-stat att-stat-l">
            <div class="att-stat-num">{{ $cL }}</div>
            <div class="att-stat-lbl">LATE</div>
        </div>
        <div class="att-stat att-stat-e">
            <div class="att-stat-num">{{ $cE }}</div>
            <div class="att-stat-lbl">EXCUSED</div>
        </div>
    </div>

    {{-- ── PROGRESS ─────────────────────────────────────── --}}
    <div class="att-prog-wrap">
        <div class="att-prog-meta">
            <span class="att-prog-label {{ $allPresent ? 'done' : '' }}">
                @if ($allPresent)
                    ✓ All {{ $total }} present
                @else
                    {{ $cA + $cL + $cE }} exception{{ ($cA + $cL + $cE) !== 1 ? 's' : '' }} / {{ $total }} students
                @endif
            </span>
            <span style="font-size:11px;font-family:'DM Mono',monospace;font-weight:700;color:#c9a84c">{{ $pct }}%</span>
        </div>
        <div class="att-prog-track">
            <div class="att-prog-fill {{ $allPresent ? 'done' : '' }}" style="width:{{ $pct }}%"></div>
        </div>
    </div>

    @if ($alreadyMarked)
        <div class="att-notice">
            <span>ℹ</span>
            <span>Attendance already recorded for this date — saving will <strong>update</strong> existing records.</span>
        </div>
    @endif

    @if ($total > 0)

        {{-- ── CONTROLS ─────────────────────────────────── --}}
        <div class="att-ctrl-row">
            <div class="att-search-box">
                <span class="att-search-icon">⌕</span>
                <input type="text" placeholder="Search name or roll number…" x-model="search" autocomplete="off" />
            </div>
            <div class="att-markall-wrap">
                <button class="att-markall-btn" type="button" @click="ddOpen = !ddOpen">All ▾</button>
                <div class="att-dropdown" x-show="ddOpen" x-transition @click.away="ddOpen=false" style="display:none">
                    <button class="att-dd-item p" type="button" wire:click="markAllPresent" @click="ddOpen=false">✓ &nbsp;All Present</button>
                    <button class="att-dd-item a" type="button" wire:click="markAllAbsent"  @click="ddOpen=false">— &nbsp;All Absent</button>
                    <button class="att-dd-item l" type="button" wire:click="markAllLate"    @click="ddOpen=false">L &nbsp;All Late</button>
                </div>
            </div>
        </div>

        {{-- ── LEGEND ───────────────────────────────────── --}}
        <div class="att-legend">
            <div class="att-leg-item">
                <div class="att-leg-sym" style="background:#f0fdf4;border-color:#86efac;color:#16a34a;font-size:14px">✓</div>
                Present
            </div>
            <div class="att-leg-item">
                <div class="att-leg-sym" style="background:#fef2f2;border-color:#fca5a5;color:#dc2626;font-size:16px">—</div>
                Absent
            </div>
            <div class="att-leg-item">
                <div class="att-leg-sym" style="background:#fffbeb;border-color:#fde68a;color:#d97706;font-size:10px;font-family:'DM Mono',monospace;font-weight:800">L</div>
                Late
            </div>
            <div class="att-leg-item">
                <div class="att-leg-sym" style="background:#f5f3ff;border-color:#c4b5fd;color:#7c3aed;font-size:10px;font-family:'DM Mono',monospace;font-weight:800">E</div>
                Excused
            </div>
            <div class="att-leg-hint">tap row to cycle</div>
        </div>

        {{-- ── EXCEPTIONS PANEL ─────────────────────────── --}}
        @if (count($exceptions) > 0)
            <div class="att-exc-panel">
                <div class="att-exc-head">
                    <span class="att-exc-title">EXCEPTIONS</span>
                    <span class="att-exc-count">{{ count($exceptions) }} student{{ count($exceptions) !== 1 ? 's' : '' }}</span>
                </div>
                @foreach ($exceptions as $s)
                    @php $st = $attendance[$s['id']] ?? 'present'; @endphp
                    <div class="att-exc-item">
                        <div class="att-exc-roll">{{ $s['roll_number'] }}</div>
                        <div class="att-exc-name">{{ $s['name'] }}</div>
                        <div class="att-exc-sym {{ $st }}">{{ $SYM[$st] ?? '?' }}</div>
                        <button
                            class="att-exc-undo"
                            type="button"
                            wire:click="setStatus({{ $s['id'] }}, 'present')"
                            title="Reset to Present"
                        >↩</button>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ── REGISTER TABLE ───────────────────────────── --}}
        {{-- Table header (sticky) --}}
        <div style="position:sticky;top:0;z-index:80;padding-bottom:4px">
            <div class="att-reg-head" style="border-radius:10px 10px 0 0">
                <span>NO.</span>
                <span>STUDENT NAME</span>
                <span style="text-align:center">ATT.</span>
            </div>
        </div>

        {{-- Groups of 10 --}}
        @foreach ($groups as $gi => $group)
            @php
                $startRoll = $group[0]['roll_number'];
                $endRoll   = end($group)['roll_number'];
            @endphp

            {{-- Group divider (skip for first group) --}}
            @if ($gi > 0)
                <div class="att-group-div"
                     x-show="!search"
                     x-transition>
                    <div class="att-group-line"></div>
                    <div class="att-group-label">ROLL {{ $startRoll }} – {{ $endRoll }}</div>
                    <div class="att-group-line"></div>
                </div>
            @endif

            <div class="att-reg-table-wrap" style="{{ $gi === 0 ? 'border-radius:0 0 12px 12px;border-top:none' : '' }}">
                @foreach ($group as $idx => $student)
                    @php
                        $sid = $student['id'];
                        $st  = $attendance[$sid] ?? 'present';
                        $sym = $SYM[$st] ?? '✓';

                        // Cycle map for this row
                        $cycleMap = ['present' => 'absent', 'absent' => 'late', 'late' => 'excused', 'excused' => 'present'];
                        $nextStatus = $cycleMap[$st];
                    @endphp
                    <div
                        class="att-s-row {{ $st }}"
                        wire:click="setStatus({{ $sid }}, '{{ $nextStatus }}')"
                        data-name="{{ strtolower($student['name']) }}"
                        data-roll="{{ strtolower($student['roll_number']) }}"
                        x-show="!search || $el.dataset.name.includes(search.toLowerCase()) || $el.dataset.roll.includes(search.toLowerCase())"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        id="att-row-{{ $sid }}"
                    >
                        {{-- Roll --}}
                        <div class="att-s-roll">{{ $student['roll_number'] }}</div>

                        {{-- Name + status sub-label --}}
                        <div class="att-s-name-wrap">
                            <div class="att-s-name">{{ $student['name'] }}</div>
                            <div class="att-s-sub">
                                @if ($st === 'absent')  ✗ Absent
                                @elseif ($st === 'late')    ⏱ Late
                                @elseif ($st === 'excused') ✓ Excused
                                @endif
                            </div>
                        </div>

                        {{-- Mark cell — the register tick --}}
                        <div class="att-mark-wrap">
                            <div class="att-mark {{ $st }}" id="att-mark-{{ $sid }}">{{ $sym }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach

    @else
        <x-filament::section>
            <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">No students found in this class.</p>
        </x-filament::section>
    @endif

    {{-- ── SAVE BAR ─────────────────────────────────────── --}}
    <div class="att-save-bar">
        <div class="att-sb-info {{ $allPresent ? 'done' : '' }}">
            @if ($alreadyMarked && $allPresent)
                ✓ All {{ $total }} present — updating records
            @elseif ($allPresent)
                ✓ All {{ $total }} students present today
            @else
                {{ $cA + $cL + $cE }} exception{{ ($cA + $cL + $cE) !== 1 ? 's' : '' }} · {{ $cP }} present
            @endif
        </div>
        <button class="att-sb-btn" type="button" wire:click="submit" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="submit">
                {{ $alreadyMarked ? 'Update →' : 'Submit →' }}
            </span>
            <span wire:loading wire:target="submit">Saving…</span>
        </button>
    </div>

    @endif

</div>

<script>
// Pop animation on Livewire DOM patch
document.addEventListener('livewire:navigated', initPop);
document.addEventListener('DOMContentLoaded', initPop);

function initPop() {
    document.querySelectorAll('.att-s-row').forEach(row => {
        row.addEventListener('click', function() {
            const sid = this.id.replace('att-row-', '');
            const mark = document.getElementById('att-mark-' + sid);
            if (mark) {
                mark.classList.remove('popping');
                void mark.offsetWidth;
                mark.classList.add('popping');
                mark.addEventListener('animationend', () => mark.classList.remove('popping'), { once: true });
            }
        });
    });
}
</script>

</x-filament-panels::page>

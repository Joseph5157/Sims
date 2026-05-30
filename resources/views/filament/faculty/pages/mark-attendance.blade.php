<x-filament-panels::page>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;600&display=swap');

.att-ui *, .att-ui *::before, .att-ui *::after { box-sizing: border-box; }

/* ── INFO BAR ── */
.att-info-bar {
    background: #0f172a; border-radius: 14px;
    padding: 16px 20px; margin-bottom: 10px;
    display: flex; align-items: center; justify-content: space-between;
}
.att-ib-class { color: #94a3b8; font-size: 10px; letter-spacing: 1px; font-family: 'DM Mono', monospace; margin-bottom: 3px; text-transform: uppercase; }
.att-ib-name  { color: #fff; font-size: 17px; font-weight: 800; font-family: 'DM Sans', sans-serif; }
.att-ib-right { text-align: right; }
.att-ib-date    { color: #3b82f6; font-size: 10px; font-family: 'DM Mono', monospace; }
.att-ib-teacher { color: #475569; font-size: 10px; margin-top: 2px; font-family: 'DM Sans', sans-serif; }

/* ── PROGRESS ── */
.att-progress-wrap {
    background: #0f172a; border-radius: 14px;
    padding: 14px 20px; margin-bottom: 10px;
}
.att-prog-row   { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.att-prog-counts { display: flex; gap: 14px; }
.att-pc   { font-size: 12px; font-family: 'DM Mono', monospace; font-weight: 700; }
.att-pc-p { color: #22c55e; }
.att-pc-a { color: #ef4444; }
.att-pc-l { color: #f59e0b; }
.att-pc-e { color: #a855f7; }
.att-prog-label      { font-size: 11px; font-family: 'DM Mono', monospace; color: #64748b; font-weight: 700; }
.att-prog-label.done { color: #22c55e; }
.att-prog-track { height: 4px; background: #1e293b; border-radius: 4px; overflow: hidden; }
.att-prog-fill      { height: 100%; background: #3b82f6; border-radius: 4px; transition: width .4s ease, background .4s; }
.att-prog-fill.done { background: #22c55e; }

/* ── NOTICE ── */
.att-notice {
    display: flex; align-items: center; gap: 10px;
    background: #1e3a5f; border-radius: 10px;
    padding: 10px 14px; margin-bottom: 10px;
    color: #93c5fd; font-size: 11px; font-weight: 600; font-family: 'DM Sans', sans-serif;
}

/* ── CONTROLS ── */
.att-ctrl-row { display: flex; gap: 8px; margin-bottom: 10px; align-items: center; }
.att-search-box {
    flex: 1; display: flex; align-items: center; gap: 8px;
    background: #fff; border: 1.5px solid #e2e8f0;
    border-radius: 10px; padding: 0 12px;
}
.att-search-box input {
    border: none; outline: none; font-size: 13px; color: #0f172a;
    background: transparent; width: 100%; padding: 10px 0;
    font-family: 'DM Sans', sans-serif;
}
.dark .att-search-box { background: #1e293b; border-color: #334155; }
.dark .att-search-box input { color: #f1f5f9; }
.att-search-icon { color: #94a3b8; font-size: 16px; flex-shrink: 0; }

/* ── MARK-ALL DROPDOWN ── */
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
    background: #0f172a; border-radius: 12px; overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,.3); z-index: 200; min-width: 160px;
}
.att-dd-item {
    display: block; width: 100%; padding: 12px 16px;
    border: none; background: none; cursor: pointer;
    text-align: left; font-size: 13px; font-weight: 700;
    font-family: 'DM Sans', sans-serif;
    border-bottom: 1px solid #1e293b;
}
.att-dd-item:last-child { border-bottom: none; }
.att-dd-item.p { color: #22c55e; }
.att-dd-item.a { color: #ef4444; }
.att-dd-item.l { color: #f59e0b; }

/* ── LATE HINT ── */
.att-late-hint {
    display: flex; align-items: flex-start; gap: 8px;
    background: #1e3a5f; border-radius: 9px;
    padding: 9px 14px; margin-bottom: 12px;
    color: #93c5fd; font-size: 11px; line-height: 1.55;
    font-family: 'DM Sans', sans-serif;
}

/* ── SECTION HEADERS ── */
.att-section-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 4px 2px; margin-bottom: 6px; margin-top: 8px;
}
.att-section-title { font-size: 10px; font-weight: 700; color: #94a3b8; letter-spacing: 1px; font-family: 'DM Mono', monospace; }
.att-section-count { font-size: 10px; font-weight: 700; font-family: 'DM Mono', monospace; }

/* ── STUDENT ROW ── */
.att-student-row {
    display: flex; align-items: center; gap: 10px;
    background: #fff; border-radius: 12px; padding: 10px 14px; margin-bottom: 6px;
    border: 1.5px solid #e2e8f0;
    transition: border-color .2s, background .2s;
}
.att-student-row.absent  { border-color: rgba(252,165,165,.5); background: #fff8f8; }
.att-student-row.late    { border-color: rgba(253,230,138,.5); background: #fffdf0; }
.att-student-row.excused { border-color: rgba(196,181,253,.5); background: #faf5ff; }
.dark .att-student-row          { background: #1e293b; border-color: #334155; }
.dark .att-student-row.absent   { background: #2d1515; border-color: rgba(252,165,165,.3); }
.dark .att-student-row.late     { background: #2d2510; border-color: rgba(253,230,138,.3); }
.dark .att-student-row.excused  { background: #1e1530; border-color: rgba(196,181,253,.3); }

/* ── AVATAR ── */
.att-avatar {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 800; font-family: 'DM Mono', monospace;
    transition: background .2s, color .2s;
}

/* ── STUDENT INFO ── */
.att-s-info  { flex: 1; min-width: 0; }
.att-s-name  { font-size: 13px; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'DM Sans', sans-serif; }
.dark .att-s-name { color: #f1f5f9; }
.att-s-roll  { font-size: 10px; color: #94a3b8; font-family: 'DM Mono', monospace; margin-top: 1px; }
.att-s-status { font-size: 10px; font-weight: 700; margin-top: 2px; font-family: 'DM Sans', sans-serif; }

/* ── LATE CHIP ── */
.att-late-chip {
    padding: 4px 9px; border-radius: 20px; flex-shrink: 0;
    font-size: 9px; font-weight: 800; font-family: 'DM Mono', monospace;
    border: 1.5px solid #fde68a; background: #fefce8; color: #d97706;
    white-space: nowrap; cursor: pointer; transition: all .15s;
}
.att-late-chip.active  { background: #d97706; color: #fff; border-color: #d97706; }
.att-late-chip:active  { transform: scale(.94); }

/* ── EXCUSED BADGE ── */
.att-excused-chip {
    padding: 4px 9px; border-radius: 20px; flex-shrink: 0;
    font-size: 9px; font-weight: 800; font-family: 'DM Mono', monospace;
    border: 1.5px solid #c4b5fd; background: #f5f3ff; color: #7c3aed;
}

/* ── iOS TOGGLE ── */
.att-toggle {
    width: 48px; height: 27px; border-radius: 14px; flex-shrink: 0;
    background: #e2e8f0; position: relative;
    transition: background .25s; cursor: pointer;
}
.att-toggle.on         { background: #22c55e; }
.att-toggle.late-state { background: #f59e0b; }
.att-toggle-thumb {
    position: absolute; top: 3px; left: 3px;
    width: 21px; height: 21px; border-radius: 50%;
    background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,.2);
    transition: transform .25s cubic-bezier(.4,0,.2,1);
}
.att-toggle.on .att-toggle-thumb         { transform: translateX(21px); }
.att-toggle.late-state .att-toggle-thumb { transform: translateX(21px); }
.att-toggle-l {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%,-50%) translateX(10px);
    font-size: 8px; font-weight: 900; color: #fff;
    font-family: 'DM Mono', monospace; pointer-events: none;
}

/* ── SAVE BAR ── */
.att-save-bar {
    position: sticky; bottom: 0;
    background: #fff; border-top: 1px solid #e2e8f0;
    padding: 12px 0 16px; margin-top: 12px;
    display: flex; align-items: center; gap: 12px; z-index: 50;
}
.dark .att-save-bar { background: #0f172a; border-top-color: #1e293b; }
.att-sb-info { flex: 1; font-size: 12px; font-weight: 600; color: #64748b; font-family: 'DM Sans', sans-serif; }
.att-sb-info.done { color: #16a34a; }
.att-sb-btn {
    padding: 12px 24px; border-radius: 11px;
    background: #0f172a; color: #fff; border: none;
    font-size: 13px; font-weight: 800; font-family: 'DM Sans', sans-serif;
    cursor: pointer; box-shadow: 0 4px 14px rgba(15,23,42,.2);
    transition: opacity .2s, transform .1s;
}
.att-sb-btn:disabled     { opacity: .6; cursor: not-allowed; }
.att-sb-btn:active:not(:disabled) { transform: scale(.97); }
</style>

<div class="att-ui" x-data="{ search: '', ddOpen: false }">

    {{-- ── CONFIG SECTION ── --}}
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

    {{-- ── COMPUTED ── --}}
    @php
        $className    = $facultyClasses[$selectedClassId] ?? 'Class';
        $teacherName  = auth()->user()->name ?? '';
        $total        = count($students);
        $cP = $this->getPresentCount();
        $cA = $this->getAbsentCount();
        $cL = $this->getLateCount();
        $cE = $this->getExcusedCount();
        $allPresent   = ($cA + $cL + $cE) === 0;
        $exceptions   = array_values(array_filter($students, fn($s) => in_array($attendance[$s['id']] ?? 'present', ['absent','late','excused'])));
        $pct          = $total > 0 ? round(($cP / $total) * 100) : 0;
    @endphp

    {{-- ── INFO BAR ── --}}
    <div class="att-info-bar">
        <div>
            <div class="att-ib-class">{{ strtoupper($className) }}</div>
            <div class="att-ib-name">Attendance</div>
        </div>
        <div class="att-ib-right">
            <div class="att-ib-date">{{ \Carbon\Carbon::parse($attendanceDate)->format('D, d M Y') }}</div>
            <div class="att-ib-teacher">{{ $teacherName }}</div>
        </div>
    </div>

    {{-- ── PROGRESS ── --}}
    <div class="att-progress-wrap">
        <div class="att-prog-row">
            <div class="att-prog-counts">
                <span class="att-pc att-pc-p">P:{{ $cP }}</span>
                <span class="att-pc att-pc-a">A:{{ $cA }}</span>
                <span class="att-pc att-pc-l">L:{{ $cL }}</span>
                @if ($cE > 0)
                    <span class="att-pc att-pc-e">E:{{ $cE }}</span>
                @endif
            </div>
            <span class="att-prog-label {{ $allPresent ? 'done' : '' }}">
                @if ($allPresent)
                    All {{ $total }} present ✓
                @else
                    {{ $cA + $cL }} exception{{ ($cA + $cL) !== 1 ? 's' : '' }} / {{ $total }}
                @endif
            </span>
        </div>
        <div class="att-prog-track">
            <div class="att-prog-fill {{ $allPresent ? 'done' : '' }}" style="width:{{ $pct }}%"></div>
        </div>
    </div>

    @if ($alreadyMarked)
        <div class="att-notice">
            <span>ℹ</span>
            <span>Attendance already recorded — saving will <strong>update</strong> existing records.</span>
        </div>
    @endif

    {{-- ── CONTROLS ── --}}
    <div class="att-ctrl-row">
        <div class="att-search-box">
            <span class="att-search-icon">⌕</span>
            <input type="text" placeholder="Search name or roll…" x-model="search" autocomplete="off" />
        </div>
        <div class="att-markall-wrap">
            <button class="att-markall-btn" type="button" @click="ddOpen = !ddOpen">All ▾</button>
            <div class="att-dropdown" x-show="ddOpen" x-transition @click.away="ddOpen=false" style="display:none">
                <button class="att-dd-item p" type="button" wire:click="markAllPresent" @click="ddOpen=false">✓ All Present</button>
                <button class="att-dd-item a" type="button" wire:click="markAllAbsent" @click="ddOpen=false">✗ All Absent</button>
                <button class="att-dd-item l" type="button" wire:click="markAllLate"   @click="ddOpen=false">⏱ All Late</button>
            </div>
        </div>
    </div>

    {{-- ── LATE HINT ── --}}
    <div class="att-late-hint">
        <span>💡</span>
        <span>
            <strong>Toggle ON</strong> = Present &nbsp;·&nbsp; <strong>Toggle OFF</strong> = Absent<br/>
            When absent, tap <strong style="color:#f59e0b">Late?</strong> to mark as Late instead.
        </span>
    </div>

    @if ($total > 0)

        {{-- ── EXCEPTIONS ── --}}
        @if (count($exceptions) > 0)
            <div class="att-section-head">
                <span class="att-section-title">EXCEPTIONS</span>
                <span class="att-section-count" style="color:#ef4444">
                    {{ count($exceptions) }} student{{ count($exceptions) !== 1 ? 's' : '' }}
                </span>
            </div>

            @foreach ($exceptions as $student)
                @php
                    $st = $attendance[$student['id']] ?? 'present';
                    $ini = collect(explode(' ', $student['name']))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->join('');
                    $avBg    = match($st) { 'absent' => '#fca5a5', 'late' => '#fde68a', 'excused' => '#ddd6fe', default => '#f0fdf4' };
                    $avColor = match($st) { 'absent' => '#dc2626', 'late' => '#d97706', 'excused' => '#7c3aed', default => '#16a34a' };
                    $toggleTarget = $st === 'present' ? 'absent' : 'present';
                    $lateTarget   = $st === 'late' ? 'absent' : 'late';
                @endphp
                <div class="att-student-row {{ $st }}"
                     data-name="{{ strtolower($student['name']) }}"
                     data-roll="{{ strtolower($student['roll_number']) }}"
                     x-show="!search || $el.dataset.name.includes(search.toLowerCase()) || $el.dataset.roll.includes(search.toLowerCase())">
                    <div class="att-avatar" style="background:{{ $avBg }};color:{{ $avColor }}">{{ $ini }}</div>
                    <div class="att-s-info">
                        <div class="att-s-name">{{ $student['name'] }}</div>
                        <div class="att-s-roll">Roll {{ $student['roll_number'] }}</div>
                        <div class="att-s-status">
                            @if ($st === 'absent')  <span style="color:#ef4444">✗ Absent</span>
                            @elseif ($st === 'late') <span style="color:#d97706">⏱ Late</span>
                            @elseif ($st === 'excused') <span style="color:#7c3aed">✓ Excused</span>
                            @endif
                        </div>
                    </div>
                    @if ($st === 'excused')
                        <span class="att-excused-chip">Excused</span>
                    @else
                        <button class="att-late-chip {{ $st === 'late' ? 'active' : '' }}"
                                type="button"
                                wire:click="setStatus({{ $student['id'] }}, '{{ $lateTarget }}')"
                                @click.stop>
                            {{ $st === 'late' ? '⏱ Late' : 'Late?' }}
                        </button>
                    @endif
                    <div class="att-toggle {{ $st === 'present' ? 'on' : ($st === 'late' ? 'late-state' : '') }}"
                         wire:click="setStatus({{ $student['id'] }}, '{{ $toggleTarget }}')">
                        <div class="att-toggle-thumb"></div>
                        @if ($st === 'late') <div class="att-toggle-l">L</div> @endif
                    </div>
                </div>
            @endforeach
        @endif

        {{-- ── ALL STUDENTS ── --}}
        @php $presentOnly = array_filter($students, fn($s) => ($attendance[$s['id']] ?? 'present') === 'present'); @endphp
        <div class="att-section-head">
            <span class="att-section-title">ALL STUDENTS</span>
            <span class="att-section-count" style="color:#22c55e">{{ count($presentOnly) }} present</span>
        </div>

        @foreach ($students as $student)
            @php
                $st = $attendance[$student['id']] ?? 'present';
                $ini = collect(explode(' ', $student['name']))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->join('');
                $avBg    = match($st) { 'absent' => '#fca5a5', 'late' => '#fde68a', 'excused' => '#ddd6fe', default => '#f0fdf4' };
                $avColor = match($st) { 'absent' => '#dc2626', 'late' => '#d97706', 'excused' => '#7c3aed', default => '#16a34a' };
                $toggleTarget = $st === 'present' ? 'absent' : 'present';
                $lateTarget   = $st === 'late' ? 'absent' : 'late';
            @endphp
            <div class="att-student-row {{ $st }}"
                 data-name="{{ strtolower($student['name']) }}"
                 data-roll="{{ strtolower($student['roll_number']) }}"
                 x-show="!search || $el.dataset.name.includes(search.toLowerCase()) || $el.dataset.roll.includes(search.toLowerCase())">
                <div class="att-avatar" style="background:{{ $avBg }};color:{{ $avColor }}">{{ $ini }}</div>
                <div class="att-s-info">
                    <div class="att-s-name">{{ $student['name'] }}</div>
                    <div class="att-s-roll">Roll {{ $student['roll_number'] }}</div>
                    @if ($st !== 'present')
                        <div class="att-s-status">
                            @if ($st === 'absent') <span style="color:#ef4444">✗ Absent</span>
                            @elseif ($st === 'late') <span style="color:#d97706">⏱ Late</span>
                            @elseif ($st === 'excused') <span style="color:#7c3aed">✓ Excused</span>
                            @endif
                        </div>
                    @endif
                </div>
                @if ($st === 'excused')
                    <span class="att-excused-chip">Excused</span>
                @elseif (in_array($st, ['absent','late']))
                    <button class="att-late-chip {{ $st === 'late' ? 'active' : '' }}"
                            type="button"
                            wire:click="setStatus({{ $student['id'] }}, '{{ $lateTarget }}')"
                            @click.stop>
                        {{ $st === 'late' ? '⏱ Late' : 'Late?' }}
                    </button>
                @endif
                <div class="att-toggle {{ $st === 'present' ? 'on' : ($st === 'late' ? 'late-state' : '') }}"
                     wire:click="setStatus({{ $student['id'] }}, '{{ $toggleTarget }}')">
                    <div class="att-toggle-thumb"></div>
                    @if ($st === 'late') <div class="att-toggle-l">L</div> @endif
                </div>
            </div>
        @endforeach

    @else
        <x-filament::section>
            <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">No students found in this class.</p>
        </x-filament::section>
    @endif

    {{-- ── SAVE BAR ── --}}
    <div class="att-save-bar">
        <div class="att-sb-info {{ $allPresent ? 'done' : '' }}">
            @if ($alreadyMarked && $allPresent)
                ✓ All {{ $total }} present — updating records
            @elseif ($allPresent)
                ✓ All {{ $total }} students present
            @else
                {{ $cA + $cL }} exception{{ ($cA + $cL) !== 1 ? 's' : '' }} out of {{ $total }} students
            @endif
        </div>
        <button class="att-sb-btn" type="button" wire:click="submit" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="submit">
                {{ $alreadyMarked ? 'Update →' : 'Save →' }}
            </span>
            <span wire:loading wire:target="submit">Saving…</span>
        </button>
    </div>

    @endif

</div>
</x-filament-panels::page>

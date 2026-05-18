<?php

namespace App\Filament\Student\Pages;

use App\Models\Notice;
use App\Models\Student;
use App\Models\User;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MyNotices extends Page
{
    protected string $view = 'filament.student.pages.my-notices';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'Notices';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Notices';

    public bool $hasProfile = false;

    public array $notices = [];

    public array $profile = [];

    /** IDs of notices whose full body is currently expanded */
    public array $expandedNotices = [];

    // --------------------------------------------------------------------------
    // Lifecycle
    // --------------------------------------------------------------------------

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $student = $user?->studentProfile;

        if (! $student) {
            return;
        }

        $this->hasProfile = true;
        $student->loadMissing('collegeClass');

        $this->profile = [
            'name' => $user->name,
            'class' => $student->collegeClass?->name ?? '—',
        ];

        $this->loadNotices($student);
    }

    // --------------------------------------------------------------------------
    // Actions
    // --------------------------------------------------------------------------

    public function toggleNotice(int $id): void
    {
        if (in_array($id, $this->expandedNotices, true)) {
            $this->expandedNotices = array_values(
                array_filter($this->expandedNotices, fn ($i) => $i !== $id)
            );
        } else {
            $this->expandedNotices[] = $id;
        }
    }

    // --------------------------------------------------------------------------
    // Data loader
    // --------------------------------------------------------------------------

    private function loadNotices(Student $student): void
    {
        $notices = Notice::where(function ($q): void {
            // target = 'all' or 'student' — never 'faculty'
            $q->where('target', 'all')
                ->orWhere('target', 'student');
        })
            ->where(function ($q) use ($student): void {
                // global notices (no class restriction) OR specific to student's class
                $q->whereNull('college_class_id')
                    ->orWhere('college_class_id', $student->college_class_id);
            })
            ->where(function ($q): void {
                // published: NULL published_at treated as always visible (matches dashboard behaviour)
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderByRaw('CASE WHEN published_at IS NULL THEN 0 ELSE 1 END')  // NULL (always-on) first tier
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $this->notices = $notices->map(function (Notice $n) use ($student): array {
            $body = $n->body ?? '';
            $isLong = Str::length($body) > 150;
            $preview = $isLong ? Str::limit($body, 150) : $body;
            $targetValue = $n->target?->value ?? 'all';
            $isClassSpec = $n->college_class_id !== null;

            // Decide badge label + colour
            if ($isClassSpec) {
                $badgeLabel = $student->collegeClass?->name ?? 'Your Class';
                $badgeClass = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300';
            } elseif ($targetValue === 'student') {
                $badgeLabel = 'Students';
                $badgeClass = 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300';
            } else {
                $badgeLabel = 'All Students';
                $badgeClass = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300';
            }

            // Left border colour
            $borderClass = ($targetValue === 'all' && ! $isClassSpec)
                ? 'border-l-4 border-l-blue-400 dark:border-l-blue-500'
                : 'border-l-4 border-l-violet-500 dark:border-l-violet-400';

            // Human-readable date
            $date = $n->published_at
                ? Carbon::parse($n->published_at)->diffForHumans()
                : Carbon::parse($n->created_at)->diffForHumans();

            return [
                'id' => $n->id,
                'title' => $n->title,
                'body' => $body,
                'preview' => $preview,
                'is_long' => $isLong,
                'date' => $date,
                'badge_label' => $badgeLabel,
                'badge_class' => $badgeClass,
                'border_class' => $borderClass,
            ];
        })->toArray();
    }

    // --------------------------------------------------------------------------
    // Access guard
    // --------------------------------------------------------------------------

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && method_exists($user, 'hasRole') && $user->hasRole('student');
    }
}

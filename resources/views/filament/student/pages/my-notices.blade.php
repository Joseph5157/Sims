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
{{-- PAGE HEADER                                                                 --}}
{{-- ========================================================================== --}}
<div class="overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-violet-600 to-purple-700 shadow-lg">
    <div class="relative px-6 py-5">
        <div class="pointer-events-none absolute -right-8 -top-8 h-40 w-40 rounded-full bg-white/5"></div>
        <div class="pointer-events-none absolute -bottom-6 right-24 h-24 w-24 rounded-full bg-white/5"></div>
        <div class="relative flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/15 backdrop-blur-sm">
                <x-heroicon-o-bell class="h-6 w-6 text-white" />
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-white">Notices</h1>
                    @if (count($notices) > 0)
                        <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold text-white">
                            {{ count($notices) }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-violet-200">
                    {{ $profile['name'] }}
                    <span class="opacity-60">·</span>
                    {{ $profile['class'] }}
                </p>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================== --}}
{{-- EMPTY STATE                                                                 --}}
{{-- ========================================================================== --}}
@if (count($notices) === 0)
    <div class="flex flex-col items-center justify-center gap-4 rounded-2xl bg-white py-20 text-center shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
            <x-heroicon-o-bell-slash class="h-8 w-8 text-gray-400 dark:text-gray-500" />
        </div>
        <div>
            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">No notices yet</p>
            <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">
                Notices from school will appear here.
            </p>
        </div>
    </div>

@else

{{-- ========================================================================== --}}
{{-- NOTICES LIST                                                                --}}
{{-- ========================================================================== --}}
<div class="space-y-3">
    @foreach ($notices as $notice)
        @php $isExpanded = in_array($notice['id'], $expandedNotices, true); @endphp

        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 transition-shadow hover:shadow-md dark:bg-gray-900 dark:ring-gray-700 {{ $notice['border_class'] }}">
            <div class="px-5 py-4">

                {{-- ---------------------------------------------------------- --}}
                {{-- HEADER ROW: title + date + badge                           --}}
                {{-- ---------------------------------------------------------- --}}
                <div class="flex flex-wrap items-start justify-between gap-3">

                    {{-- Title --}}
                    <h3 class="text-base font-bold leading-snug text-gray-900 dark:text-gray-100">
                        {{ $notice['title'] }}
                    </h3>

                    {{-- Date --}}
                    <span class="flex-shrink-0 text-xs text-gray-400 dark:text-gray-500">
                        {{ $notice['date'] }}
                    </span>

                </div>

                {{-- Target badge --}}
                <div class="mt-2">
                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $notice['badge_class'] }}">
                        <x-heroicon-m-users class="h-3 w-3" />
                        {{ $notice['badge_label'] }}
                    </span>
                </div>

                {{-- ---------------------------------------------------------- --}}
                {{-- BODY TEXT                                                   --}}
                {{-- ---------------------------------------------------------- --}}
                @if ($notice['body'])
                    <div class="mt-3">
                        <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                            @if ($isExpanded || ! $notice['is_long'])
                                {{ $notice['body'] }}
                            @else
                                {{ $notice['preview'] }}
                            @endif
                        </p>

                        @if ($notice['is_long'])
                            <button
                                wire:click="toggleNotice({{ $notice['id'] }})"
                                class="mt-2 text-xs font-semibold text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300"
                            >
                                @if ($isExpanded)
                                    <span class="flex items-center gap-1">
                                        <x-heroicon-m-chevron-up class="h-3 w-3" />
                                        Show less
                                    </span>
                                @else
                                    <span class="flex items-center gap-1">
                                        <x-heroicon-m-chevron-down class="h-3 w-3" />
                                        Read more
                                    </span>
                                @endif
                            </button>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    @endforeach
</div>

@endif
{{-- end notices --}}

@endif
{{-- end hasProfile --}}

</x-filament-panels::page>

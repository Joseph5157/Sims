@php $classes = $this->getTodayClasses(); @endphp

<x-filament-widgets::widget class="fi-wi-today-attendance">
    @if (count($classes) > 0)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    Mark Today's Attendance
                </div>
            </x-slot>
            <x-slot name="description">
                {{ now()->format('l, d F Y') }}
            </x-slot>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mt-2">
                @foreach ($classes as $class)
                    <div class="flex items-center justify-between rounded-xl border p-4 gap-3
                        {{ $class['marked']
                            ? 'border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-700'
                            : 'border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-600' }}">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                {{ $class['name'] }}
                            </p>
                            <p class="text-xs mt-0.5 {{ $class['marked'] ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }} font-medium">
                                {{ $class['marked'] ? '✓ Marked' : '⚠ Not Marked' }}
                            </p>
                        </div>
                        @if (! $class['marked'])
                            <a
                                href="{{ $class['url'] }}"
                                class="flex-shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-3 py-2 text-xs font-bold text-white hover:bg-primary-700 transition-colors shadow-sm"
                            >
                                Mark Now
                                <x-heroicon-m-arrow-right class="w-3.5 h-3.5" />
                            </a>
                        @else
                            <span class="flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/40">
                                <x-heroicon-m-check class="w-4 h-4 text-green-600 dark:text-green-400" />
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>

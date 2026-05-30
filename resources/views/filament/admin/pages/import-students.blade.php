<x-filament-panels::page>
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>

    @if (! empty($lastImportResults))
        <x-filament::section heading="Import Results">
            @if (! empty($lastImportResults['imported']))
                <div class="mb-4 rounded-lg border border-green-300 bg-green-50 p-4">
                    <h4 class="font-semibold text-green-900">
                        ✓ Successfully Imported ({{ count($lastImportResults['imported']) }})
                    </h4>
                    <ul class="mt-2 space-y-1 text-sm text-green-800">
                        @foreach ($lastImportResults['imported'] as $item)
                            <li>{{ $item['email'] }} — {{ $item['name'] }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (! empty($lastImportResults['skipped']))
                <div class="mb-4 rounded-lg border border-yellow-300 bg-yellow-50 p-4">
                    <h4 class="font-semibold text-yellow-900">
                        ⚠ Skipped ({{ count($lastImportResults['skipped']) }})
                    </h4>
                    <ul class="mt-2 space-y-1 text-sm text-yellow-800">
                        @foreach ($lastImportResults['skipped'] as $item)
                            <li>{{ $item['email'] }} — {{ $item['reason'] }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (! empty($lastImportResults['errors']))
                <div class="rounded-lg border border-red-300 bg-red-50 p-4">
                    <h4 class="font-semibold text-red-900">
                        ✕ Errors ({{ count($lastImportResults['errors']) }})
                    </h4>
                    <ul class="mt-2 space-y-1 text-sm text-red-800">
                        @foreach ($lastImportResults['errors'] as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </x-filament::section>
    @endif
</x-filament-panels::page>

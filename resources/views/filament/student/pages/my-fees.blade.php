<x-filament-panels::page>
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

<div class="overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-violet-600 to-purple-700 shadow-lg">
    <div class="relative px-6 py-5">
        <div class="pointer-events-none absolute -right-8 -top-8 h-40 w-40 rounded-full bg-white/5"></div>
        <div class="pointer-events-none absolute -bottom-6 right-24 h-24 w-24 rounded-full bg-white/5"></div>
        <div class="relative flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/15 backdrop-blur-sm">
                <x-heroicon-o-banknotes class="h-6 w-6 text-white" />
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">My Fees</h1>
                <p class="text-sm text-violet-200">
                    {{ $profile['name'] }}
                    <span class="opacity-60">•</span>
                    {{ $profile['class'] }}
                    @if ($profile['academic_year'] !== '—')
                        <span class="opacity-60">•</span>
                        {{ $profile['academic_year'] }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

@if (! $hasFees)
    <div class="flex flex-col items-center justify-center gap-4 rounded-2xl bg-white py-20 text-center shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
            <x-heroicon-o-banknotes class="h-8 w-8 text-gray-400 dark:text-gray-500" />
        </div>
        <div>
            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">No fee information available</p>
            <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">
                Fee details will appear here once assigned by admin.
            </p>
        </div>
    </div>

@else

<div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    <div class="flex flex-col gap-1.5 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
            <x-heroicon-o-document-text class="h-4 w-4 text-gray-600 dark:text-gray-400" />
        </div>
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Fees Due</p>
        <p class="text-xl font-extrabold text-gray-900 dark:text-gray-100">
            ₹{{ number_format($summary['total_due'], 2) }}
        </p>
    </div>

    <div class="flex flex-col gap-1.5 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
            <x-heroicon-o-tag class="h-4 w-4 text-green-600 dark:text-green-400" />
        </div>
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Discounts</p>
        <p class="text-xl font-extrabold text-green-600 dark:text-green-400">
            ₹{{ number_format($summary['total_discount'], 2) }}
        </p>
    </div>

    <div class="flex flex-col gap-1.5 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
            <x-heroicon-o-check-badge class="h-4 w-4 text-blue-600 dark:text-blue-400" />
        </div>
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Paid</p>
        <p class="text-xl font-extrabold text-blue-600 dark:text-blue-400">
            ₹{{ number_format($summary['total_paid'], 2) }}
        </p>
    </div>

    <div class="flex flex-col gap-1.5 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg
            {{ $summary['balance_due'] > 0 ? 'bg-red-100 dark:bg-red-900/30' : 'bg-green-100 dark:bg-green-900/30' }}">
            @if ($summary['balance_due'] > 0)
                <x-heroicon-o-exclamation-circle class="h-4 w-4 text-red-600 dark:text-red-400" />
            @else
                <x-heroicon-o-check-circle class="h-4 w-4 text-green-600 dark:text-green-400" />
            @endif
        </div>
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Balance Due</p>
        <p class="text-xl font-extrabold {{ $summary['balance_class'] }}">
            ₹{{ number_format($summary['balance_due'], 2) }}
        </p>
    </div>
</div>

<div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
    <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/30">
            <x-heroicon-o-table-cells class="h-4 w-4 text-violet-600 dark:text-violet-400" />
        </div>
        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fee Breakdown</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[720px] text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Category</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Amount</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-green-600 dark:text-green-400">Discount</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Net Payable</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">Paid</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Balance</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Due Date</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @foreach ($feeRows as $row)
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/40">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $row['category'] }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $row['frequency'] }}</p>
                        </td>
                        <td class="px-4 py-4 text-right font-medium text-gray-700 dark:text-gray-300">
                            ₹{{ number_format($row['amount'], 2) }}
                        </td>
                        <td class="px-4 py-4 text-right font-medium text-green-600 dark:text-green-400">
                            @if ($row['discount'] > 0)
                                − ₹{{ number_format($row['discount'], 2) }}
                            @else
                                <span class="text-gray-300 dark:text-gray-700">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right font-semibold text-gray-800 dark:text-gray-200">
                            ₹{{ number_format($row['net_payable'], 2) }}
                        </td>
                        <td class="px-4 py-4 text-right font-medium text-blue-600 dark:text-blue-400">
                            @if ($row['amount_paid'] > 0)
                                ₹{{ number_format($row['amount_paid'], 2) }}
                            @else
                                <span class="text-gray-300 dark:text-gray-700">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right font-semibold
                            {{ $row['balance'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                            @if ($row['balance'] > 0)
                                ₹{{ number_format($row['balance'], 2) }}
                            @else
                                <x-heroicon-m-check class="ml-auto h-4 w-4" />
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center text-xs text-gray-500 dark:text-gray-400">
                            {{ $row['due_date'] }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $row['status_class'] }}">
                                {{ $row['status'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/60">
                    <td class="px-5 py-3.5 text-xs font-bold uppercase text-gray-600 dark:text-gray-400">Total</td>
                    <td class="px-4 py-3.5 text-right font-bold text-gray-800 dark:text-gray-200">
                        ₹{{ number_format($summary['total_due'], 2) }}
                    </td>
                    <td class="px-4 py-3.5 text-right font-bold text-green-600 dark:text-green-400">
                        @if ($summary['total_discount'] > 0)
                            − ₹{{ number_format($summary['total_discount'], 2) }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-right font-bold text-gray-800 dark:text-gray-200">
                        ₹{{ number_format($summary['total_due'] - $summary['total_discount'], 2) }}
                    </td>
                    <td class="px-4 py-3.5 text-right font-bold text-blue-600 dark:text-blue-400">
                        ₹{{ number_format($summary['total_paid'], 2) }}
                    </td>
                    <td class="px-4 py-3.5 text-right font-bold {{ $summary['balance_class'] }}">
                        ₹{{ number_format($summary['balance_due'], 2) }}
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if (count($paymentHistory) > 0)
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
        <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                <x-heroicon-o-receipt-percent class="h-4 w-4 text-blue-600 dark:text-blue-400" />
            </div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Payment History</h3>
            <span class="ml-auto rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                {{ count($paymentHistory) }} {{ count($paymentHistory) === 1 ? 'payment' : 'payments' }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[580px] text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Receipt</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">For</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Mode</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-red-500 dark:text-red-400">Fine</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @foreach ($paymentHistory as $payment)
                        <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/40">
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    {{ $payment['receipt_number'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-gray-600 dark:text-gray-400">
                                {{ $payment['date'] }}
                            </td>
                            <td class="px-4 py-3.5 text-gray-600 dark:text-gray-400">
                                {{ $payment['category'] }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-bold text-gray-900 dark:text-gray-100">
                                ₹{{ number_format($payment['amount_paid'], 2) }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $payment['payment_mode_badge'] }}">
                                    {{ $payment['payment_mode'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right text-xs">
                                @if ($payment['fine_amount'] > 0)
                                    <span class="font-semibold text-red-600 dark:text-red-400">
                                        ₹{{ number_format($payment['fine_amount'], 2) }}
                                    </span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-700">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@if (count($discounts) > 0)
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
        <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                <x-heroicon-o-tag class="h-4 w-4 text-green-600 dark:text-green-400" />
            </div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Applied Discounts</h3>
        </div>

        <div class="divide-y divide-gray-50 dark:divide-gray-800">
            @foreach ($discounts as $discount)
                <div class="flex flex-wrap items-start gap-4 px-5 py-4">
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $discount['type_class'] }}">
                        {{ $discount['type_label'] }}
                    </span>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $discount['category'] }}
                        </p>
                        @if ($discount['reason'] !== '—')
                            <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                                {{ $discount['reason'] }}
                            </p>
                        @endif
                    </div>

                    <div class="flex-shrink-0 text-right">
                        @if ($discount['amount'] !== null && $discount['amount'] > 0)
                            <p class="font-bold text-green-600 dark:text-green-400">
                                − ₹{{ number_format($discount['amount'], 2) }}
                            </p>
                        @endif
                        @if ($discount['percentage'] !== null && $discount['percentage'] > 0)
                            <p class="font-bold text-green-600 dark:text-green-400">
                                {{ $discount['percentage'] }}% off
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@endif
@endif

</x-filament-panels::page>

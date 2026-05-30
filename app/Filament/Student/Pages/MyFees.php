<?php

namespace App\Filament\Student\Pages;

use App\Models\AcademicYear;
use App\Models\FeeDiscount;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MyFees extends Page
{
    protected string $view = 'filament.student.pages.my-fees';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'My Fees';

    protected static ?int $navigationSort = 6;

    protected static ?string $title = 'My Fees';

    public bool $hasProfile = false;

    public bool $hasFees = false;

    public array $profile = [];

    public array $summary = [];

    public array $feeRows = [];

    public array $paymentHistory = [];

    public array $discounts = [];

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $student = $user?->studentProfile;

        if (! $student) {
            return;
        }

        $this->hasProfile = true;
        $student->loadMissing(['collegeClass', 'academicYear']);

        $this->profile = [
            'name' => $user->name,
            'class' => $student->collegeClass?->name ?? '—',
            'academic_year' => $student->academicYear?->name
                ?? AcademicYear::where('is_current', true)->value('name')
                ?? '—',
        ];

        $this->loadFees($student);
    }

    private function loadFees(Student $student): void
    {
        $yearId = $student->academic_year_id
            ?? AcademicYear::where('is_current', true)->value('id');

        $structureQuery = FeeStructure::where('college_class_id', $student->college_class_id)
            ->with('feeCategory');

        if ($yearId) {
            $structureQuery->where(function ($q) use ($yearId): void {
                $q->where('academic_year_id', $yearId)
                    ->orWhereNull('academic_year_id');
            });
        }

        $structures = $structureQuery->orderBy('id')->get();

        if ($structures->isEmpty()) {
            return;
        }

        $this->hasFees = true;
        $structureIds = $structures->pluck('id');
        $today = now()->toDateString();

        $payments = FeePayment::where('student_id', $student->id)
            ->whereIn('fee_structure_id', $structureIds)
            ->with('feeStructure.feeCategory')
            ->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $discountsRaw = FeeDiscount::where('student_id', $student->id)
            ->whereIn('fee_structure_id', $structureIds)
            ->get();

        $paymentsByStructure = $payments->groupBy('fee_structure_id');
        $discountsByStructure = $discountsRaw->groupBy('fee_structure_id');

        $rows = [];
        $grandDue = 0.0;
        $grandDiscount = 0.0;
        $grandPaid = 0.0;

        foreach ($structures as $structure) {
            $amount = (float) $structure->amount;
            $structPayments = $paymentsByStructure->get($structure->id, collect());
            $structDiscounts = $discountsByStructure->get($structure->id, collect());

            $discountFixed = (float) $structDiscounts->sum(fn ($d) => (float) ($d->amount ?? 0));
            $discountPctSum = (float) $structDiscounts->sum(fn ($d) => (float) ($d->percentage ?? 0));
            $discountFromPct = $amount * $discountPctSum / 100;
            $totalDiscount = round($discountFixed + $discountFromPct, 2);
            $netPayable = round(max(0.0, $amount - $totalDiscount), 2);

            $amountPaid = round((float) $structPayments->sum('amount_paid'), 2);
            $balance = round(max(0.0, $netPayable - $amountPaid), 2);

            $dueDate = $structure->due_date?->toDateString();

            [$status, $statusClass] = match (true) {
                $balance <= 0 => ['Paid', 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
                $amountPaid > 0 => ['Partial', 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'],
                ($dueDate && $dueDate < $today) => ['Overdue', 'bg-red-200 text-red-800 dark:bg-red-900/40 dark:text-red-200'],
                default => ['Pending', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
            };

            $rows[] = [
                'id' => $structure->id,
                'category' => $structure->feeCategory?->name ?? '—',
                'frequency' => $structure->frequency?->label() ?? '—',
                'amount' => $amount,
                'discount' => $totalDiscount,
                'net_payable' => $netPayable,
                'amount_paid' => $amountPaid,
                'balance' => $balance,
                'due_date' => $structure->due_date?->format('d M Y') ?? '—',
                'status' => $status,
                'status_class' => $statusClass,
            ];

            $grandDue += $amount;
            $grandDiscount += $totalDiscount;
            $grandPaid += $amountPaid;
        }

        $this->feeRows = $rows;

        $grandBalance = round(max(0.0, $grandDue - $grandDiscount - $grandPaid), 2);

        $this->summary = [
            'total_due' => round($grandDue, 2),
            'total_discount' => round($grandDiscount, 2),
            'total_paid' => round($grandPaid, 2),
            'balance_due' => $grandBalance,
            'balance_class' => $grandBalance > 0
                ? 'text-red-600 dark:text-red-400'
                : 'text-green-600 dark:text-green-400',
        ];

        $this->paymentHistory = $payments->map(function (FeePayment $p): array {
            $modeVal = $p->payment_mode?->value ?? 'cash';

            return [
                'receipt_number' => $p->receipt_number ?? '—',
                'date' => $p->payment_date?->format('d M Y') ?? '—',
                'category' => $p->feeStructure?->feeCategory?->name ?? '—',
                'amount_paid' => (float) $p->amount_paid,
                'payment_mode' => $p->payment_mode?->label() ?? '—',
                'payment_mode_badge' => $this->paymentModeBadgeClass($modeVal),
                'fine_amount' => (float) ($p->fine_amount ?? 0),
                'tax_amount' => (float) ($p->tax_amount ?? 0),
            ];
        })->toArray();

        $this->discounts = $discountsRaw->map(function (FeeDiscount $d) use ($structures): array {
            $structure = $structures->firstWhere('id', $d->fee_structure_id);

            return [
                'type_label' => $d->discount_type?->label() ?? 'Other',
                'type_class' => $this->discountBadgeClass($d->discount_type?->value),
                'amount' => $d->amount !== null ? (float) $d->amount : null,
                'percentage' => $d->percentage !== null ? (float) $d->percentage : null,
                'reason' => $d->reason ?? '—',
                'category' => $structure?->feeCategory?->name ?? '—',
            ];
        })->toArray();
    }

    private function discountBadgeClass(?string $type): string
    {
        return match ($type) {
            'scholarship' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            'sibling' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'staff_ward' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
            default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
        };
    }

    private function paymentModeBadgeClass(string $mode): string
    {
        return match ($mode) {
            'online' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'cash' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
            'cheque' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
            'dd' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300',
            default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
        };
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && method_exists($user, 'hasRole') && $user->hasRole('student');
    }
}

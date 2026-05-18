<?php

namespace App\Models;

use App\Enums\PaymentMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'amount_paid',
        'payment_date',
        'payment_mode',
        'receipt_number',
        'fine_amount',
        'tax_amount',
        'fine_reason',
        'collected_by',
        'notes',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'payment_date' => 'date',
        'payment_mode' => PaymentMode::class,
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public static function generateReceiptNumber(): string
    {
        $year = now()->year;
        $prefix = "RCPT-{$year}-";

        $last = static::where('receipt_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('receipt_number');

        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}

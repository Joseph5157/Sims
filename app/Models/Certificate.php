<?php

namespace App\Models;

use App\Enums\CertificateStatus;
use App\Enums\CertificateType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'certificate_type',
        'certificate_number',
        'issued_date',
        'issued_by',
        'reason',
        'remarks',
        'status',
    ];

    protected $casts = [
        'certificate_type' => CertificateType::class,
        'status' => CertificateStatus::class,
        'issued_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}

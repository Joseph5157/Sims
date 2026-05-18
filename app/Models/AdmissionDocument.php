<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_id',
        'document_type',
        'submitted',
        'verified',
    ];

    protected $casts = [
        'submitted' => 'boolean',
        'verified' => 'boolean',
    ];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }
}

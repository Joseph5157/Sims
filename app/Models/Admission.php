<?php

namespace App\Models;

use App\Enums\AdmissionStatus;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
        'enquiry_id',
        'student_name',
        'date_of_birth',
        'gender',
        'parent_name',
        'phone',
        'email',
        'address',
        'previous_school',
        'previous_class',
        'applying_for_class_id',
        'academic_year_id',
        'status',
        'admission_date',
        'documents_submitted',
        'remarks',
    ];

    protected $casts = [
        'gender' => Gender::class,
        'status' => AdmissionStatus::class,
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'documents_submitted' => 'array',
    ];

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function applyingForClass(): BelongsTo
    {
        return $this->belongsTo(CollegeClass::class, 'applying_for_class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function admissionDocuments(): HasMany
    {
        return $this->hasMany(AdmissionDocument::class);
    }
}

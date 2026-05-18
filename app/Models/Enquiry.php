<?php

namespace App\Models;

use App\Enums\EnquirySource;
use App\Enums\EnquiryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_name',
        'parent_name',
        'phone',
        'email',
        'applying_for_class_id',
        'academic_year_id',
        'source',
        'status',
        'enquiry_date',
        'notes',
        'assigned_to',
    ];

    protected $casts = [
        'source' => EnquirySource::class,
        'status' => EnquiryStatus::class,
        'enquiry_date' => 'date',
    ];

    public function applyingForClass(): BelongsTo
    {
        return $this->belongsTo(CollegeClass::class, 'applying_for_class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }
}

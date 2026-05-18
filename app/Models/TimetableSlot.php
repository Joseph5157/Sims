<?php

namespace App\Models;

use App\Enums\DayOfWeek;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimetableSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'college_class_id',
        'subject_id',
        'faculty_id',
        'academic_year_id',
        'day_of_week',
        'period_number',
        'start_time',
        'end_time',
        // legacy columns kept for backward compat
        'day',
        'period',
        'room',
    ];

    protected $casts = [
        'day_of_week' => DayOfWeek::class,
        'period_number' => 'integer',
    ];

    public function collegeClass(): BelongsTo
    {
        return $this->belongsTo(CollegeClass::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}

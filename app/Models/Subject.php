<?php

namespace App\Models;

use App\Enums\GradingType;
use App\Enums\SubjectType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'department_id',
        'college_class_id',
        'faculty_id',
        'credits',
        'description',
        'subject_type',
        'grading_type',
        'is_active',
    ];

    protected $casts = [
        'subject_type' => SubjectType::class,
        'grading_type' => GradingType::class,
        'is_active' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function collegeClass(): BelongsTo
    {
        return $this->belongsTo(CollegeClass::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function timetableSlots(): HasMany
    {
        return $this->hasMany(TimetableSlot::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}

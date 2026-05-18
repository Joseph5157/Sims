<?php

namespace App\Models;

use App\Enums\ExamGroupType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'college_class_id',
        'academic_year_id',
        'exam_type',
        'start_date',
        'end_date',
        'conducted_date',
        'is_published',
    ];

    protected $casts = [
        'type' => ExamGroupType::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'conducted_date' => 'date',
        'is_published' => 'boolean',
    ];

    public function collegeClass(): BelongsTo
    {
        return $this->belongsTo(CollegeClass::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}

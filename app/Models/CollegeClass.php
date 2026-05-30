<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CollegeClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'section',
        'department_id',
        'semester',
        'academic_year',
        'academic_year_id',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function examGroups(): HasMany
    {
        return $this->hasMany(ExamGroup::class);
    }
}

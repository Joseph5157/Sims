<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'college_class_id',
        'exam_type',
        'start_date',
        'end_date',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function collegeClass(): BelongsTo
    {
        return $this->belongsTo(CollegeClass::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}

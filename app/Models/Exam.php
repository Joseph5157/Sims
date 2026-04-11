<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_group_id',
        'subject_id',
        'date',
        'start_time',
        'end_time',
        'maximum_marks',
        'minimum_marks',
        'weightage',
    ];

    public function examGroup(): BelongsTo
    {
        return $this->belongsTo(ExamGroup::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function examScores(): HasMany
    {
        return $this->hasMany(ExamScore::class);
    }

    public function getPassPercentage(): float
    {
        $maximumMarks = (float) $this->maximum_marks;

        if ($maximumMarks <= 0) {
            return 0.0;
        }

        return ((float) $this->minimum_marks / $maximumMarks) * 100;
    }
}

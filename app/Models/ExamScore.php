<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'marks_obtained',
        'grading_level_id',
        'absent',
        'remarks',
        'writing_language',
        'entered_by',
    ];

    protected function casts(): array
    {
        return [
            'absent' => 'boolean',
            'marks_obtained' => 'decimal:2',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function gradingLevel(): BelongsTo
    {
        return $this->belongsTo(GradingLevel::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function getPercentage(): float
    {
        $maximumMarks = (float) ($this->exam?->maximum_marks ?? 0);

        if ($maximumMarks <= 0) {
            return 0.0;
        }

        return ((float) ($this->marks_obtained ?? 0) / $maximumMarks) * 100;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradingLevel extends Model
{
    protected $fillable = [
        'name',
        'min_score',
        'max_score',
        'grade_point',
        'college_class_id',
    ];

    public function collegeClass(): BelongsTo
    {
        return $this->belongsTo(CollegeClass::class);
    }

    public function examScores(): HasMany
    {
        return $this->hasMany(ExamScore::class);
    }

    public static function calculateGrade(float $percentage, ?int $classId): ?self
    {
        $percentage = max(0, min(100, $percentage));

        $query = static::query()
            ->where('min_score', '<=', $percentage)
            ->where('max_score', '>=', $percentage);

        if ($classId !== null) {
            $classSpecific = (clone $query)
                ->where('college_class_id', $classId)
                ->orderByDesc('max_score')
                ->first();

            if ($classSpecific !== null) {
                return $classSpecific;
            }
        }

        return (clone $query)
            ->whereNull('college_class_id')
            ->orderByDesc('max_score')
            ->first();
    }
}

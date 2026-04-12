<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Student extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'roll_number',
        'department_id',
        'college_class_id',
        'date_of_birth',
        'phone',
        'address',
        'admission_year',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function collegeClass()
    {
        return $this->belongsTo(CollegeClass::class, 'college_class_id');
    }

    public function disciplineCases()
    {
        return $this->hasMany(DisciplineCase::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function guardians()
    {
        return $this->hasMany(Guardian::class);
    }

    public function subjects()
    {
        return $this->hasManyThrough(
            Subject::class,
            CollegeClass::class,
            'id',
            'college_class_id',
            'college_class_id',
            'id'
        );
    }

    public function getAttendancePercentage(): float
    {
        $attendances = $this->attendances()->get();

        $total = $attendances->count();
        if ($total === 0) {
            return 0;
        }

        $present = $attendances->where('status', 'present')->count();

        return round(($present / $total) * 100, 1);
    }

    public function getDaysNeededForAttendanceThreshold(float $threshold = 75.0): int
    {
        $attendances = $this->attendances()->get();
        $present = $attendances->whereIn('status', ['present', 'late', 'excused'])->count();
        $absent = $attendances->where('status', 'absent')->count();
        $total = $attendances->count();

        if ($total === 0) {
            return 0;
        }

        $currentPercentage = ($present / $total) * 100;

        // If already above threshold, no days needed
        if ($currentPercentage >= $threshold) {
            return 0;
        }

        // Calculate consecutive days needed to reach threshold
        // Formula: x = (threshold * total - 100 * present) / (100 - threshold)
        $thresholdDecimal = $threshold / 100;
        $daysNeeded = ceil(($thresholdDecimal * $total - $present) / (1 - $thresholdDecimal));

        return max(0, $daysNeeded);
    }

    public function getLowAttendanceSubjects(float $threshold = 75.0)
    {
        return $this->subjects()
            ->withCount(['attendances' => function ($q) {
                $q->where('status', 'present');
            }])
            ->get()
            ->filter(function ($subject) use ($threshold) {
                $attendances = $this->attendances()
                    ->where('college_class_id', $this->college_class_id)
                    ->get();

                if ($attendances->isEmpty()) {
                    return false;
                }

                $present = $attendances->where('status', 'present')->count();
                $percentage = ($present / $attendances->count()) * 100;

                return $percentage < $threshold;
            });
    }
}

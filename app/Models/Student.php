<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

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

    public function getAttendancePercentageAttribute($subjectId = null)
    {
        $query = $this->attendances();

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        $total = $query->count();

        if ($total === 0) {
            return 0;
        }

        $present = $this->attendances()
            ->when($subjectId, fn ($attendanceQuery) => $attendanceQuery->where('subject_id', $subjectId))
            ->where('status', 'present')
            ->count();

        return round(($present / $total) * 100, 1);
    }

    public function getLowAttendanceSubjects()
    {
        return $this->subjects()->get()->filter(function ($subject) {
            return $this->getAttendancePercentageAttribute($subject->id) < 75;
        });
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function subjects()
    {
        return $this->hasManyThrough(Subject::class, CollegeClass::class, 'id', 'college_class_id');
    }
}

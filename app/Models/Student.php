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

    public function getLowAttendanceSubjects()
    {
        return collect();
    }
}

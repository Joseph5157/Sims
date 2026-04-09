<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'college_class_id',
        'attendance_date',
        'status',
        'notes',
        'marked_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function collegeClass()
    {
        return $this->belongsTo(CollegeClass::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}

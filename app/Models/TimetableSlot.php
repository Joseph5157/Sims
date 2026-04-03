<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'college_class_id',
        'subject_id',
        'faculty_id',
        'day',
        'period',
        'room',
    ];

    public function collegeClass()
    {
        return $this->belongsTo(CollegeClass::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
}

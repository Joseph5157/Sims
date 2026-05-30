<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'college_class_id',
        'attendance_date',
        'status',
        'notes',
        'edit_reason',
        'marked_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function collegeClass(): BelongsTo
    {
        return $this->belongsTo(CollegeClass::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}

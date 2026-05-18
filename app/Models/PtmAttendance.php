<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PtmAttendance extends Model
{
    use HasFactory;

    protected $table = 'ptm_attendance';

    protected $fillable = [
        'ptm_schedule_id',
        'student_id',
        'parent_attended',
        'faculty_notes',
        'follow_up_required',
    ];

    protected $casts = [
        'parent_attended' => 'boolean',
        'follow_up_required' => 'boolean',
    ];

    public function ptmSchedule(): BelongsTo
    {
        return $this->belongsTo(PtmSchedule::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

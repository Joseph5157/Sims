<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PtmSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'academic_year_id',
        'scheduled_date',
        'college_class_id',
        'created_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function collegeClass(): BelongsTo
    {
        return $this->belongsTo(CollegeClass::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ptmAttendances(): HasMany
    {
        return $this->hasMany(PtmAttendance::class);
    }
}

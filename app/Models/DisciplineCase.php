<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplineCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'faculty_id',
        'title',
        'description',
        'severity',
        'attachment',
        'status',
        'admin_id',
        'resolved_at',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'section',
        'department_id',
        'semester',
        'academic_year',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}

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
}

<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Student extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'roll_number',
        'admission_number',
        'department_id',
        'college_class_id',
        'academic_year_id',
        'date_of_birth',
        'gender',
        'blood_group',
        'phone',
        'address',
        'admission_year',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'gender' => Gender::class,
        'status' => StudentStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function collegeClass(): BelongsTo
    {
        return $this->belongsTo(CollegeClass::class, 'college_class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function disciplineCases(): HasMany
    {
        return $this->hasMany(DisciplineCase::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class);
    }

    public function feePayments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function feeDiscounts(): HasMany
    {
        return $this->hasMany(FeeDiscount::class);
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
        $totalMarked = $this->attendances()
            ->whereIn('status', ['present', 'late', 'excused', 'absent'])
            ->count();

        if ($totalMarked === 0) {
            return 0.0;
        }

        $present = $this->attendances()
            ->whereIn('status', ['present', 'late', 'excused'])
            ->count();

        return round(($present / $totalMarked) * 100, 1);
    }

    public function getLowAttendanceSubjects()
    {
        return collect();
    }

    public function getOutstandingAmount(): float
    {
        $structures = FeeStructure::where('college_class_id', $this->college_class_id)->get();

        $outstanding = 0.0;

        foreach ($structures as $structure) {
            $paid = (float) $this->feePayments()
                ->where('fee_structure_id', $structure->id)
                ->sum('amount_paid');

            $discountFixed = (float) $this->feeDiscounts()
                ->where('fee_structure_id', $structure->id)
                ->whereNotNull('amount')
                ->sum('amount');

            $discountPct = (float) $this->feeDiscounts()
                ->where('fee_structure_id', $structure->id)
                ->whereNotNull('percentage')
                ->sum('percentage');

            $discountFromPct = ($structure->amount * $discountPct) / 100;
            $effectiveAmount = max(0, (float) $structure->amount - $discountFixed - $discountFromPct);

            $outstanding += max(0, $effectiveAmount - $paid);
        }

        return round($outstanding, 2);
    }
}

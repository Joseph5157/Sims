<?php

namespace App\Models;

use App\Enums\MessageToType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_user_id',
        'to_type',
        'college_class_id',
        'student_id',
        'subject',
        'body',
        'sent_at',
    ];

    protected $casts = [
        'to_type' => MessageToType::class,
        'sent_at' => 'datetime',
    ];

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function collegeClass(): BelongsTo
    {
        return $this->belongsTo(CollegeClass::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

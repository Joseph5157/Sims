<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'student_id',
        'participated',
        'achievement',
        'remarks',
    ];

    protected $casts = [
        'participated' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

<?php

namespace App\Models;

use App\Enums\NotificationChannel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'trigger_event',
        'channel',
        'body_template',
        'is_active',
    ];

    protected $casts = [
        'channel' => NotificationChannel::class,
        'is_active' => 'boolean',
    ];
}

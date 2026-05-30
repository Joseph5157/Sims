<?php

namespace App\Enums;

enum NotificationChannel: string
{
    case WhatsApp = 'whatsapp';
    case SMS = 'sms';
    case Email = 'email';
    case InApp = 'in_app';

    public function label(): string
    {
        return match ($this) {
            self::WhatsApp => 'WhatsApp',
            self::SMS => 'SMS',
            self::Email => 'Email',
            self::InApp => 'In-App',
        };
    }
}

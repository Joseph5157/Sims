<?php

namespace App\Mail;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttendanceWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Student $student,
        public Guardian $guardian,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Attendance Warning – '.(string) ($this->student->user?->name ?? $this->student->roll_number),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.attendance-warning',
        );
    }
}

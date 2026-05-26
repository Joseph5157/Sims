<?php

namespace App\Mail;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttendanceWarningMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Pass computed stats explicitly — SerializesModels re-fetches Student/Guardian
     * from the DB when the job runs, which would lose the dynamically set properties
     * (attendance_percentage, absent_count, etc.) that are not DB columns.
     */
    public function __construct(
        public Student $student,
        public Guardian $guardian,
        public float $attendancePercentage,
        public int $absentCount,
        public int $totalCount,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Attendance Warning – '.$this->student->user->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.attendance-warning',
        );
    }
}

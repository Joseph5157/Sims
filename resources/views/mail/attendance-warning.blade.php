<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Warning</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #991b1b; padding: 28px 32px; }
        .header h1 { color: #ffffff; margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 0.02em; }
        .header p { color: #fca5a5; margin: 6px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .greeting { font-size: 16px; color: #111827; margin-bottom: 16px; }
        .info-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 20px 24px; margin-bottom: 24px; }
        .info-box .label { font-size: 12px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 4px; }
        .info-box .value { font-size: 22px; font-weight: 700; color: #991b1b; }
        .info-box .sub { font-size: 13px; color: #6b7280; margin-top: 4px; }
        .stats { display: flex; gap: 16px; margin-bottom: 24px; }
        .stat { flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 14px 16px; text-align: center; }
        .stat .num { font-size: 20px; font-weight: 700; color: #111827; }
        .stat .lbl { font-size: 12px; color: #6b7280; margin-top: 2px; }
        .message { font-size: 14px; color: #374151; line-height: 1.7; margin-bottom: 24px; }
        .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 20px 32px; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Attendance Warning Notice</h1>
            <p>College Management System</p>
        </div>

        <div class="body">
            <p class="greeting">
                Dear {{ $guardian->fullName() }},
            </p>

            <p class="message">
                We are writing to inform you that your ward,
                <strong>{{ $student->user?->name ?? $student->roll_number }}</strong>
                (Roll No: <strong>{{ $student->roll_number }}</strong>,
                Class: <strong>{{ $student->collegeClass?->name ?? 'N/A' }}</strong>),
                has fallen below the minimum required attendance of <strong>75%</strong>.
            </p>

            <div class="info-box">
                <div class="label">Current Attendance</div>
                <div class="value">{{ $student->attendance_percentage ?? 0 }}%</div>
                <div class="sub">
                    Minimum required: 75% &nbsp;|&nbsp;
                    Shortfall: {{ $student->shortfall ?? 0 }} class day(s) needed to recover
                </div>
            </div>

            <div class="stats">
                <div class="stat">
                    <div class="num">{{ $student->present_count ?? 0 }}</div>
                    <div class="lbl">Days Present</div>
                </div>
                <div class="stat">
                    <div class="num">{{ $student->absent_count ?? 0 }}</div>
                    <div class="lbl">Days Absent</div>
                </div>
                <div class="stat">
                    <div class="num">{{ $student->total_count ?? 0 }}</div>
                    <div class="lbl">Total Marked</div>
                </div>
            </div>

            <p class="message">
                Please ensure your ward attends all upcoming classes regularly. Students who fail to
                maintain the minimum attendance requirement may be barred from appearing in examinations.
                Kindly contact the college administration if you have any concerns.
            </p>
        </div>

        <div class="footer">
            This is an automated message from the College Management System. Please do not reply to this email.
        </div>
    </div>
</body>
</html>

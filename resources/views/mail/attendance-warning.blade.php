@component('mail::message')
# Attendance Warning

Dear {{ $guardian->fullName() }},

This is to inform you that your ward **{{ $student->user->name }}**
({{ $student->collegeClass->name ?? 'N/A' }}, Roll No. {{ $student->roll_number }})
has recorded an attendance of **{{ $attendancePercentage }}%**, which is below the
required minimum of **75%**.

@component('mail::table')
| | |
|:--|--:|
| Total classes held | {{ $totalCount }} |
| Classes attended | {{ $totalCount - $absentCount }} |
| Absences | {{ $absentCount }} |
| Current attendance | {{ $attendancePercentage }}% |
@endcomponent

We urge you to speak with your ward and ensure regular attendance going forward.
If there is a genuine reason for the absences, please contact the administration at
the earliest.

Thanks,<br>
{{ config('app.name') }}
@endcomponent

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // attendance_date is not covered by any standalone index — only the third
        // column in the unique (student_id, college_class_id, attendance_date),
        // which MySQL cannot use for date-range scans without the leading columns.
        // The (college_class_id, attendance_date) composite covers the common
        // "show class register for a given date" query.
        Schema::table('attendances', function (Blueprint $table) {
            $table->index('attendance_date', 'attendances_attendance_date_index');
            $table->index(['college_class_id', 'attendance_date'], 'attendances_class_date_index');
        });

        // The existing unique is (exam_id, student_id). Adding the reverse lets
        // MySQL satisfy "all scores for a student across exams" without a full scan.
        Schema::table('exam_scores', function (Blueprint $table) {
            $table->index(['student_id', 'exam_id'], 'exam_scores_student_exam_index');
        });

        // day_of_week has no index at all. The composite covers the primary query
        // pattern: "fetch the timetable for a class on a specific day".
        Schema::table('timetable_slots', function (Blueprint $table) {
            $table->index('day_of_week', 'timetable_slots_day_of_week_index');
            $table->index(['college_class_id', 'day_of_week'], 'timetable_slots_class_day_index');
        });

        // grades.student_id is already indexed via the FK constraint and is the
        // leftmost column in the unique (student_id, subject_id, exam_type) index,
        // so no additional index is needed.
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('attendances_attendance_date_index');
            $table->dropIndex('attendances_class_date_index');
        });

        Schema::table('exam_scores', function (Blueprint $table) {
            $table->dropIndex('exam_scores_student_exam_index');
        });

        Schema::table('timetable_slots', function (Blueprint $table) {
            $table->dropIndex('timetable_slots_day_of_week_index');
            $table->dropIndex('timetable_slots_class_day_index');
        });
    }
};

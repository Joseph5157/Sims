<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timetable_slots', function (Blueprint $table) {
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'])
                ->nullable()->after('faculty_id');
            $table->unsignedTinyInteger('period_number')->nullable()->after('day_of_week');
            $table->string('start_time', 8)->nullable()->after('period_number');
            $table->string('end_time', 8)->nullable()->after('start_time');
            $table->foreignId('academic_year_id')
                ->nullable()->after('end_time')
                ->constrained('academic_years')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('timetable_slots', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn(['day_of_week', 'period_number', 'start_time', 'end_time', 'academic_year_id']);
        });
    }
};

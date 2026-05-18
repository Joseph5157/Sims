<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ptm_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ptm_schedule_id')->constrained('ptm_schedules')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->boolean('parent_attended')->default(false);
            $table->text('faculty_notes')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->timestamps();

            $table->unique(['ptm_schedule_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ptm_attendance');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('faculties')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('college_class_id')->constrained('college_classes')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->date('week_start_date');
            $table->string('topic');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['planned', 'completed'])->default('planned');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_plans');
    }
};

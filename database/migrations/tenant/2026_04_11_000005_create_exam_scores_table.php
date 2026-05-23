<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('exam_scores')) {
            return;
        }

        Schema::create('exam_scores', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams');
            $table->foreignId('student_id')->constrained('students');
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->foreignId('grading_level_id')->nullable()->constrained('grading_levels');
            $table->boolean('absent')->default(false);
            $table->text('remarks')->nullable();
            $table->foreignId('entered_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['exam_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_scores');
    }
};

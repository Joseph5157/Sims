<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('faculty_id')
                ->nullable()
                ->after('college_class_id')
                ->constrained('faculties')
                ->nullOnDelete();

            $table->enum('subject_type', ['theory', 'practical', 'elective', 'project'])
                ->default('theory')
                ->after('faculty_id');

            $table->enum('grading_type', ['marks', 'grade', 'pass_fail'])
                ->default('marks')
                ->after('subject_type');

            $table->boolean('is_active')
                ->default(true)
                ->after('grading_type');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->dropColumn(['faculty_id', 'subject_type', 'grading_type', 'is_active']);
        });
    }
};

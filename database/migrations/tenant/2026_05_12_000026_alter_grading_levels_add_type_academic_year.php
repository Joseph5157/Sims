<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grading_levels', function (Blueprint $table) {
            $table->enum('type', ['scholastic', 'co_scholastic'])
                ->default('scholastic')
                ->after('grade_point');

            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('type')
                ->constrained('academic_years')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('grading_levels', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn(['type', 'academic_year_id']);
        });
    }
};

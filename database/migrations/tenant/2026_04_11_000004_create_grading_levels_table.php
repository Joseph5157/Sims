<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('grading_levels')) {
            return;
        }

        Schema::create('grading_levels', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->decimal('grade_point', 4, 2)->nullable();
            $table->foreignId('college_class_id')->nullable()->constrained('college_classes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_levels');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->foreignId('college_class_id')->constrained('college_classes');
            $table->enum('exam_type', ['marks', 'grades']);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_groups');
    }
};

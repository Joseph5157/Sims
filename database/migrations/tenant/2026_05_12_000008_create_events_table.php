<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('event_type', ['academic', 'cultural', 'sports', 'general']);
            $table->date('event_date');
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->enum('target', ['all', 'specific_class'])->default('all');
            $table->foreignId('college_class_id')->nullable()->constrained('college_classes')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

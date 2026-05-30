<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('certificate_type', ['tc', 'bonafide', 'study', 'character']);
            $table->string('certificate_number')->unique();
            $table->date('issued_date');
            $table->foreignId('issued_by')->constrained('users')->restrictOnDelete();
            $table->text('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['requested', 'approved', 'issued'])->default('requested');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};

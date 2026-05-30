<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->string('parent_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->foreignId('applying_for_class_id')->nullable()
                ->constrained('college_classes')->nullOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->enum('source', ['walkin', 'referral', 'online', 'phone']);
            $table->enum('status', ['new', 'followup', 'converted', 'dropped'])->default('new');
            $table->date('enquiry_date');
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('admission_number')->nullable()->unique()->after('roll_number');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->string('blood_group')->nullable()->after('gender');
            $table->enum('status', ['active', 'alumni', 'transferred', 'dropped'])
                ->default('active')->after('blood_group');
            $table->foreignId('academic_year_id')
                ->nullable()->after('college_class_id')
                ->constrained('academic_years')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn(['admission_number', 'gender', 'blood_group', 'status', 'academic_year_id']);
        });
    }
};

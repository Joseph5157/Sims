<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_groups', function (Blueprint $table) {
            $table->enum('type', ['fa', 'sa'])->nullable()->after('name');
            $table->date('conducted_date')->nullable()->after('end_date');
            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('conducted_date')
                ->constrained('academic_years')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exam_groups', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn(['type', 'conducted_date', 'academic_year_id']);
        });
    }
};

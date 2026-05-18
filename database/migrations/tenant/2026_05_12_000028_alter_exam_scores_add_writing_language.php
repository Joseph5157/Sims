<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_scores', function (Blueprint $table): void {
            $table->string('writing_language', 100)->nullable()->after('remarks');
        });
    }

    public function down(): void
    {
        Schema::table('exam_scores', function (Blueprint $table): void {
            $table->dropColumn('writing_language');
        });
    }
};

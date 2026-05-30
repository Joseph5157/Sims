<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['department_id', 'expires_at']);

            $table->renameColumn('content', 'body');

            $table->enum('target', ['all', 'faculty', 'student'])->default('all')->after('body');
            $table->foreignId('college_class_id')->nullable()->after('target')
                ->constrained('college_classes')->nullOnDelete();
            $table->timestamp('published_at')->nullable()->after('college_class_id');
        });
    }

    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropForeign(['college_class_id']);
            $table->dropColumn(['target', 'college_class_id', 'published_at']);
            $table->renameColumn('body', 'content');

            $table->foreignId('department_id')->nullable()
                ->constrained('departments')->nullOnDelete();
            $table->date('expires_at')->nullable();
        });
    }
};

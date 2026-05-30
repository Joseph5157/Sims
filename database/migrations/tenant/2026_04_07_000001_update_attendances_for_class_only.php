<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('attendances', 'subject_id')) {
            // Check if a true FK constraint exists (some deployments only have an index).
            $db = Schema::getConnection()->getDatabaseName();
            $hasFk = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', $db)
                ->where('TABLE_NAME', 'attendances')
                ->where('CONSTRAINT_NAME', 'attendances_subject_id_foreign')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->exists();

            if ($hasFk) {
                // MySQL requires dropping the foreign key BEFORE dropping a unique
                // index that covers the same column; they must be separate calls.
                Schema::table('attendances', function (Blueprint $table) {
                    $table->dropForeign(['subject_id']);
                });
            } else {
                // Only a plain index exists — drop it directly.
                $hasIndex = DB::table('information_schema.STATISTICS')
                    ->where('TABLE_SCHEMA', $db)
                    ->where('TABLE_NAME', 'attendances')
                    ->where('INDEX_NAME', 'attendances_subject_id_foreign')
                    ->exists();
                if ($hasIndex) {
                    Schema::table('attendances', function (Blueprint $table) {
                        $table->dropIndex('attendances_subject_id_foreign');
                    });
                }
            }

            // The composite unique index (student_id, subject_id, date) may be the
            // only index covering student_id, causing MySQL to refuse dropping it
            // while the student_id FK exists. Drop the FK first, then restore it.
            $hasStudentFk = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', $db)
                ->where('TABLE_NAME', 'attendances')
                ->where('CONSTRAINT_NAME', 'attendances_student_id_foreign')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->exists();

            if ($hasStudentFk) {
                Schema::table('attendances', function (Blueprint $table) {
                    $table->dropForeign(['student_id']);
                });
            }

            Schema::table('attendances', function (Blueprint $table) {
                $table->dropUnique(['student_id', 'subject_id', 'date']);
                $table->dropColumn('subject_id');
            });

            // Re-add the student_id FK if it was dropped above.
            if ($hasStudentFk) {
                Schema::table('attendances', function (Blueprint $table) {
                    $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
                });
            }
        }

        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'date')) {
                $table->renameColumn('date', 'attendance_date');
            }

            if (Schema::hasColumn('attendances', 'remarks')) {
                $table->renameColumn('remarks', 'notes');
            }

            if (! Schema::hasColumn('attendances', 'college_class_id')) {
                $table->foreignId('college_class_id')
                    ->nullable()
                    ->after('student_id')
                    ->constrained()
                    ->cascadeOnDelete();
            }
        });

        DB::statement('UPDATE attendances SET college_class_id = (SELECT college_class_id FROM students WHERE students.id = attendances.student_id) WHERE college_class_id IS NULL');

        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'sqlite') {
            DB::statement('ALTER TABLE attendances MODIFY college_class_id BIGINT UNSIGNED NOT NULL');
            DB::statement("ALTER TABLE attendances MODIFY status ENUM('present','absent','late','excused') DEFAULT 'absent'");
        } else {
            DB::statement("UPDATE attendances SET status = 'absent' WHERE status NOT IN ('present','absent','late','excused')");
        }

        Schema::table('attendances', function (Blueprint $table) {
            $table->unique(['student_id', 'college_class_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'college_class_id', 'attendance_date']);

            if (Schema::hasColumn('attendances', 'college_class_id')) {
                $table->dropForeign(['college_class_id']);
                $table->dropColumn('college_class_id');
            }

            if (Schema::hasColumn('attendances', 'attendance_date')) {
                $table->renameColumn('attendance_date', 'date');
            }

            if (Schema::hasColumn('attendances', 'notes')) {
                $table->renameColumn('notes', 'remarks');
            }

            if (! Schema::hasColumn('attendances', 'subject_id')) {
                $table->foreignId('subject_id')->after('student_id')->constrained()->cascadeOnDelete();
            }
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'sqlite') {
            DB::statement("ALTER TABLE attendances MODIFY status ENUM('present','absent','late') DEFAULT 'present'");
        }

        Schema::table('attendances', function (Blueprint $table) {
            $table->unique(['student_id', 'subject_id', 'date']);
        });
    }
};

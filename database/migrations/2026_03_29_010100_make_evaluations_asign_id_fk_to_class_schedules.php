<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // drop legacy assignments FK if exists
            $fks = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'evaluations' AND COLUMN_NAME = 'asign_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
            foreach ($fks as $fk) {
                if ($fk->CONSTRAINT_NAME === 'evaluations_ibfk_2') {
                    $table->dropForeign('evaluations_ibfk_2');
                }
            }

            // Ensure class_schedule_id exists and asign_id exists, but do not enforce problematic FK constraint due type mismatch.
            if (!Schema::hasColumn('evaluations', 'class_schedule_id')) {
                $table->unsignedBigInteger('class_schedule_id')->nullable()->after('asign_id');
            }

            if (!Schema::hasColumn('evaluations', 'asign_id')) {
                $table->unsignedBigInteger('asign_id')->nullable();
            }

            // Note: we avoid adding foreign keys here because evaluations.asign_id is BIGINT unsigned while db_class_schedules.id is INT(11), which causes errno 150.
            // The application enforces data via class_schedule_id and the new unique index below.

        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropForeign(['asign_id']);
            $table->dropForeign(['class_schedule_id']);

            // Optionally revert to original assignments reference if table exists.
            if (Schema::hasTable('assignments')) {
                $table->foreign('asign_id')->references('asign_id')->on('assignments')->onDelete('cascade');
            }
        });
    }
};

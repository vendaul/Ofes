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
            if (!Schema::hasColumn('evaluations', 'area_code')) {
                $table->string('area_code', 50)->nullable()->after('class_schedule_id');
            }
            if (!Schema::hasColumn('evaluations', 'college_id')) {
                $table->unsignedBigInteger('college_id')->nullable()->after('area_code');
            }
            if (!Schema::hasColumn('evaluations', 'period_id')) {
                $table->unsignedBigInteger('period_id')->nullable()->after('college_id');
            }
        });

        Schema::table('evaluation_results', function (Blueprint $table) {
            if (!Schema::hasColumn('evaluation_results', 'area_code')) {
                $table->string('area_code', 50)->nullable()->after('class_schedule_id');
            }
            if (!Schema::hasColumn('evaluation_results', 'college_id')) {
                $table->unsignedBigInteger('college_id')->nullable()->after('area_code');
            }
            if (!Schema::hasColumn('evaluation_results', 'period_id')) {
                $table->unsignedBigInteger('period_id')->nullable()->after('college_id');
            }
        });

        // Backfill existing rows from db_class_schedules
        DB::statement('
            UPDATE evaluations e
            JOIN db_class_schedules cs ON e.class_schedule_id = cs.id
            SET e.area_code  = cs.area_code,
                e.college_id = cs.college_id,
                e.period_id  = cs.period_id
            WHERE e.area_code IS NULL
        ');

        DB::statement('
            UPDATE evaluation_results er
            JOIN db_class_schedules cs ON er.class_schedule_id = cs.id
            SET er.area_code  = cs.area_code,
                er.college_id = cs.college_id,
                er.period_id  = cs.period_id
            WHERE er.area_code IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $columns = array_filter(['area_code', 'college_id', 'period_id'], fn($c) => Schema::hasColumn('evaluations', $c));
            if ($columns) {
                $table->dropColumn(array_values($columns));
            }
        });

        Schema::table('evaluation_results', function (Blueprint $table) {
            $columns = array_filter(['area_code', 'college_id', 'period_id'], fn($c) => Schema::hasColumn('evaluation_results', $c));
            if ($columns) {
                $table->dropColumn(array_values($columns));
            }
        });
    }
};

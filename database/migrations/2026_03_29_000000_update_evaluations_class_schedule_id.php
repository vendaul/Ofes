<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add class_schedule_id on evaluations
        if (!Schema::hasColumn('evaluations', 'class_schedule_id')) {
            Schema::table('evaluations', function (Blueprint $table) {
                $table->unsignedBigInteger('class_schedule_id')->nullable()->after('asign_id');
            });

            // backfill from existing asign_id values (assumed same ID by new behavior)
            DB::table('evaluations')->whereNull('class_schedule_id')->update(['class_schedule_id' => DB::raw('asign_id')]);
        }

        // Add class_schedule_id on evaluation_results
        if (!Schema::hasColumn('evaluation_results', 'class_schedule_id')) {
            Schema::table('evaluation_results', function (Blueprint $table) {
                if (Schema::hasColumn('evaluation_results', 'asign_id')) {
                    $table->dropForeign(['asign_id']);
                }

                $table->unsignedBigInteger('class_schedule_id')->nullable()->after('asign_id');
                $table->unique('class_schedule_id');
            });

            DB::table('evaluation_results')->whereNull('class_schedule_id')->update(['class_schedule_id' => DB::raw('asign_id')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('evaluation_results', 'class_schedule_id')) {
            Schema::table('evaluation_results', function (Blueprint $table) {
                $table->dropUnique(['class_schedule_id']);
                $table->dropColumn('class_schedule_id');
            });
        }

        if (Schema::hasColumn('evaluations', 'class_schedule_id')) {
            Schema::table('evaluations', function (Blueprint $table) {
                $table->dropColumn('class_schedule_id');
            });
        }
    }
};
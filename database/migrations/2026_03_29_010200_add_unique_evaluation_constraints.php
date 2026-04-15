<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Guard: ensure no duplicate evaluation rows exist for same student+class schedule
        $duplicates = DB::table('evaluations')
            ->select('student_id', 'class_schedule_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('class_schedule_id')
            ->groupBy('student_id', 'class_schedule_id')
            ->having('cnt', '>', 1)
            ->get();

        if ($duplicates->isNotEmpty()) {
            foreach ($duplicates as $dup) {
                $toKeep = DB::table('evaluations')
                    ->where('student_id', $dup->student_id)
                    ->where('class_schedule_id', $dup->class_schedule_id)
                    ->orderBy('eval_id')
                    ->pluck('eval_id');

                $remove = $toKeep->slice(1)->all();
                if (!empty($remove)) {
                    DB::table('evaluations')->whereIn('eval_id', $remove)->delete();
                }
            }
        }

        // also guard by legacy asign_id to avoid duplicates
        $duplicatesAsign = DB::table('evaluations')
            ->select('student_id', 'asign_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('asign_id')
            ->groupBy('student_id', 'asign_id')
            ->having('cnt', '>', 1)
            ->get();

        if ($duplicatesAsign->isNotEmpty()) {
            throw new \Exception('Cannot add unique index: duplicate student_id/asign_id values exist.');
        }

        Schema::table('evaluations', function (Blueprint $table) {
            if (!Schema::hasColumn('evaluations', 'class_schedule_id')) {
                $table->unsignedBigInteger('class_schedule_id')->nullable()->after('asign_id');
            }
            if (!Schema::hasColumn('evaluations', 'asign_id')) {
                $table->unsignedBigInteger('asign_id')->nullable();
            }

            // Add unique constraints for both pathways.
            $table->unique(['student_id', 'class_schedule_id'], 'evaluations_student_sched_unique');
            $table->unique(['student_id', 'asign_id'], 'evaluations_student_asign_unique');
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            if (Schema::hasColumn('evaluations', 'class_schedule_id')) {
                $table->dropUnique('evaluations_student_sched_unique');
            }
            if (Schema::hasColumn('evaluations', 'asign_id')) {
                $table->dropUnique('evaluations_student_asign_unique');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add supervisor assignment table for faculty-to-faculty evaluations.
        if (!Schema::hasTable('supervisor_assignments')) {
            Schema::create('supervisor_assignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('supervisor_id');
                $table->unsignedBigInteger('instructor_id');
                $table->unsignedInteger('class_schedule_id');
                $table->timestamps();

                $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('class_schedule_id')->references('id')->on('db_class_schedules')->onDelete('cascade');
                $table->unique(['supervisor_id', 'instructor_id', 'class_schedule_id'], 'unique_supervisor_assignment');
            });
        }

        // Add fields to evaluations table to support unified model and eliminate asign_id use.
        Schema::table('evaluations', function (Blueprint $table) {
            if (!Schema::hasColumn('evaluations', 'evaluator_type')) {
                $table->enum('evaluator_type', ['student', 'supervisor'])->default('student')->after('class_schedule_id');
            }
            if (!Schema::hasColumn('evaluations', 'submitted_by')) {
                $table->unsignedBigInteger('submitted_by')->nullable()->after('evaluator_type');
            }
            if (!Schema::hasColumn('evaluations', 'class_schedule_id')) {
                $table->unsignedInteger('class_schedule_id')->nullable()->after('asign_id');
            }

            if (!Schema::hasIndex('evaluations', 'evaluations_student_sched_unique')) {
                $table->unique(['student_id', 'class_schedule_id'], 'evaluations_student_sched_unique');
            }
        });

        // Prefer class_schedule_id in evaluation_results; keep asign_id for backward compatibility and update values.
        Schema::table('evaluation_results', function (Blueprint $table) {
            if (!Schema::hasColumn('evaluation_results', 'class_schedule_id')) {
                $table->unsignedInteger('class_schedule_id')->nullable()->after('asign_id');
            }
            if (!Schema::hasIndex('evaluation_results', 'evaluation_results_class_schedule_id_unique')) {
                $table->unique(['class_schedule_id'], 'evaluation_results_class_schedule_id_unique');
            }
        });

        // Drop deprecated tables if they exist.
        if (Schema::hasTable('instructor_evaluation_answers')) {
            Schema::dropIfExists('instructor_evaluation_answers');
        }
        if (Schema::hasTable('instructor_evaluations')) {
            Schema::dropIfExists('instructor_evaluations');
        }

        // Backfill from historic asign_id into class_schedule_id for existing rows
        if (Schema::hasColumn('evaluations', 'asign_id') && Schema::hasColumn('evaluations', 'class_schedule_id')) {
            DB::table('evaluations')->whereNull('class_schedule_id')->update(['class_schedule_id' => DB::raw('asign_id')]);
        }
        if (Schema::hasColumn('evaluation_results', 'asign_id') && Schema::hasColumn('evaluation_results', 'class_schedule_id')) {
            DB::table('evaluation_results')->whereNull('class_schedule_id')->update(['class_schedule_id' => DB::raw('asign_id')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('supervisor_assignments')) {
            Schema::dropIfExists('supervisor_assignments');
        }

        Schema::table('evaluations', function (Blueprint $table) {
            if (Schema::hasColumn('evaluations', 'submitted_by')) {
                $table->dropColumn('submitted_by');
            }
            if (Schema::hasColumn('evaluations', 'evaluator_type')) {
                $table->dropColumn('evaluator_type');
            }
            if (Schema::hasIndex('evaluations_student_sched_unique')) {
                $table->dropUnique('evaluations_student_sched_unique');
            }
        });

        Schema::table('evaluation_results', function (Blueprint $table) {
            if (Schema::hasColumn('evaluation_results', 'class_schedule_id')) {
                $table->dropColumn('class_schedule_id');
            }
            if (Schema::hasIndex('evaluation_results_class_schedule_id_unique')) {
                $table->dropUnique('evaluation_results_class_schedule_id_unique');
            }
        });
    }
};

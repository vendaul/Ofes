<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('db_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('area_code', 15)->nullable();
            $table->string('code', 20)->nullable();
            $table->string('course_no', 20)->nullable();
            $table->string('name', 255)->nullable();
            $table->decimal('units', 8, 2)->nullable();
            $table->decimal('load', 8, 2)->default(0.00);
            $table->decimal('tf', 8, 2)->default(0.00);
            $table->decimal('lec', 8, 2)->default(0.00);
            $table->decimal('lec_sched', 8, 2)->default(0.00);
            $table->decimal('lab', 8, 2)->default(0.00);
            $table->decimal('lab_sched', 8, 2)->default(0.00);
            $table->decimal('lab_wt', 8, 2)->default(0.00);
            $table->decimal('tot_hrs', 8, 2)->default(0.00);
            $table->string('type_id', 15)->nullable();
            $table->integer('level_id')->nullable();
            $table->integer('lec_subj_id')->nullable();
            $table->integer('college_id')->nullable();
            $table->integer('is_professional')->nullable();
            $table->integer('is_exclusive')->nullable();
            $table->integer('is_no_tuition')->nullable();
            $table->integer('is_no_grade')->nullable();
            $table->integer('is_enclose_units')->nullable();
            $table->integer('is_exclude_ave_wt')->nullable();
            $table->string('is_external_source', 1)->nullable();
            $table->integer('is_teaching')->default(0);
            $table->integer('is_ojt')->default(0);
            $table->integer('is_special')->default(0);
            $table->decimal('lab_hour_multiplier', 8, 2)->default(3.00);
            $table->decimal('lab_credit_multiplier', 8, 2)->default(0.75);
            $table->integer('is_rle')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_user_id')->nullable();
            $table->integer('updated_user_id')->nullable();
            $table->integer('deleted_user_id')->nullable();
            $table->index('id');
            $table->index(['area_code', 'college_id'], 'idx_subjects');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('db_subjects');
    }
};

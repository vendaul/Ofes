<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('db_class_schedules_student', function (Blueprint $table) {
            $table->increments('id');
            $table->string('area_code', 15)->nullable();
            $table->integer('user_student_id')->nullable();
            $table->string('year_level', 15)->nullable();
            $table->integer('period_id')->nullable();
            $table->string('term', 8)->nullable();
            $table->string('ay', 9)->nullable();
            $table->string('class_type', 10)->nullable();
            $table->string('class_status', 1)->nullable();
            $table->integer('class_schedule_id')->nullable();
            $table->string('subject_code', 15)->nullable();
            $table->string('remark', 20)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('dropped_at')->nullable();
            $table->integer('created_user_id')->nullable();
            $table->integer('updated_user_id')->nullable();
            $table->integer('deleted_user_id')->nullable();
            $table->integer('dropped_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_class_schedules_student');
    }
};

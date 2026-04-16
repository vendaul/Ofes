<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // db_class_schedules table
        Schema::create('db_class_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('area_code')->nullable();
            $table->unsignedBigInteger('period_id')->nullable();
            $table->unsignedBigInteger('college_id')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('term')->nullable();
            $table->string('ay')->nullable();
            $table->string('schedule_code')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->string('year_level')->nullable();
            $table->boolean('is_with_lec')->nullable();
            $table->string('lec_week_day')->nullable();
            $table->string('lec_start_time')->nullable();
            $table->string('lec_end_time')->nullable();
            $table->unsignedBigInteger('lec_room_id')->nullable();
            $table->boolean('is_with_lab')->nullable();
            $table->string('lab_week_day')->nullable();
            $table->string('lab_start_time')->nullable();
            $table->string('lab_end_time')->nullable();
            $table->unsignedBigInteger('lab_room_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->integer('class_size')->nullable();
            $table->integer('class_size_ireg')->nullable();
            $table->integer('class_max_size')->nullable();
            $table->integer('class_ext_size')->nullable();
            $table->boolean('is_dissolved')->nullable();
            $table->timestamps();
        });

        // db_supervisor_assignments table
        Schema::create('db_supervisor_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('class_schedule_id')->nullable();
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_user_id')->nullable();
            $table->integer('updated_user_id')->nullable();
            $table->integer('deleted_user_id')->nullable();
            $table->foreign('class_schedule_id')->references('id')->on('db_class_schedules')->onDelete('cascade');
            $table->unique(['supervisor_id', 'instructor_id', 'class_schedule_id'], 'unique_supervisor_assignment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('db_supervisor_assignments');
        Schema::dropIfExists('db_class_schedules');
    }
};

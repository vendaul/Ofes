<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('db_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('area_code', 15)->nullable();
            $table->string('category', 15)->nullable();
            $table->integer('class_id')->nullable();
            $table->string('course_program', 25)->nullable();
            $table->string('status', 15)->nullable();
            $table->string('code', 11)->nullable();
            $table->string('name', 255)->nullable();
            $table->integer('major_id')->nullable();
            $table->string('is_parent', 1)->default('N');
            $table->integer('parent_id')->nullable();
            $table->string('aop', 255)->nullable();
            $table->string('calendar', 15)->nullable();
            $table->integer('no_years')->nullable();
            $table->integer('no_of_terms')->default(3);
            $table->string('year_offered', 4)->nullable();
            $table->integer('max_unit')->nullable();
            $table->decimal('lab_units', 18, 2)->default(0.00);
            $table->decimal('lec_units', 18, 2)->default(0.00);
            $table->decimal('tot_units', 18, 2)->default(0.00);
            $table->decimal('tution_per_unit', 18, 2)->default(0.00);
            $table->decimal('fee_amount', 18, 2)->default(0.00);
            $table->integer('level_id')->nullable();
            $table->unsignedInteger('college_id')->nullable();
            $table->string('is_ched_priority', 1)->default('Y');
            $table->string('is_thesis_dissertation_required', 15)->nullable();
            $table->string('is_degree', 1)->nullable();
            $table->string('is_undergrad', 1)->nullable();
            $table->string('is_advanced_educ', 1)->default('N');
            $table->mediumText('filename')->nullable();
            $table->string('remarks', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_user_id')->nullable();
            $table->integer('updated_user_id')->nullable();
            $table->integer('deleted_user_id')->nullable();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('db_courses');
    }
};

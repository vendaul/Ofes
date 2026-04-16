<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('db_curriculum_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cs_no')->nullable();
            $table->unsignedInteger('curriculum_id')->nullable();
            $table->unsignedInteger('s_code')->nullable();
            $table->string('s_units', 4)->nullable();
            $table->string('s_year', 10)->nullable();
            $table->string('s_term', 10)->nullable();
            $table->string('s_optional', 1)->default('Y');
            $table->string('s_prerequisite', 255)->nullable();
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
        Schema::dropIfExists('db_curriculum_subjects');
    }
};

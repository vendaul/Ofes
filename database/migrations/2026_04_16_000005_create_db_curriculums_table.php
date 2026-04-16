<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('db_curriculums', function (Blueprint $table) {
            $table->increments('id');
            $table->string('area_code', 11)->nullable();
            $table->string('code', 50)->nullable();
            $table->string('desc', 255)->nullable();
            $table->unsignedInteger('college_id')->nullable();
            $table->unsignedInteger('course_id')->nullable();
            $table->string('year', 10)->nullable();
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
        Schema::dropIfExists('db_curriculums');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('db_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 11)->nullable();
            $table->string('name', 150)->nullable();
            $table->string('description', 255)->nullable();
            $table->unsignedInteger('course_id')->nullable();
            $table->unsignedInteger('year_level')->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
            $table->string('deleted_at')->nullable();
            $table->integer('created_user_id')->nullable();
            $table->integer('updated_user_id')->nullable();
            $table->integer('deleted_user_id')->nullable();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('db_sections');
    }
};

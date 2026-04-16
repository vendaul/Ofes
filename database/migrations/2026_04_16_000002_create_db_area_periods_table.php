<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('db_area_periods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('area_code', 11)->nullable();
            $table->unsignedInteger('period_id')->nullable();
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
        Schema::dropIfExists('db_area_periods');
    }
};

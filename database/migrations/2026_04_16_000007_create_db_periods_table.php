<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('db_periods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 11)->nullable();
            $table->string('name', 150)->nullable();
            $table->string('year', 9)->nullable();
            $table->string('term', 20)->nullable();
            $table->string('id_ay', 4)->nullable();
            $table->unsignedInteger('id_count')->nullable();
            $table->string('is_external', 1)->default('N');
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
        Schema::dropIfExists('db_periods');
    }
};

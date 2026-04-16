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
        Schema::table('db_subjects', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->string('year_section')->nullable();
            $table->string('lecture_day')->nullable();
            $table->string('lecture_room')->nullable();
            $table->string('laboratory_day')->nullable();
            $table->string('laboratory_room')->nullable();
            $table->enum('regular_irregular', ['regular', 'irregular'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('db_subjects', function (Blueprint $table) {
            $table->dropColumn(['description', 'year_section', 'lecture_day', 'lecture_room', 'laboratory_day', 'laboratory_room', 'regular_irregular']);
        });
    }
};

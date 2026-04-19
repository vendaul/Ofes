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
        Schema::table('db_class_schedules_student', function (Blueprint $table) {
            $table->string('term')->nullable()->after('ay');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('db_class_schedules_student', function (Blueprint $table) {
            $table->dropColumn('term');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * db_students table is imported from SQL file - no migration needed
     */
    public function up(): void
    {
        // db_students is created from SQL import, not from migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // db_students should not be dropped
    }
};

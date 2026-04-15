<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds supervisor_role (dean/program_chair) and evaluator flag.
     *
     * @return void
     */
    public function up()
    {
        // Using users table for instructors - no modifications needed
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No changes to reverse
    }
};

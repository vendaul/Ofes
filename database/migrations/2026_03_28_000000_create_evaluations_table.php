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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('asign_id')->nullable();
            $table->unsignedInteger('class_schedule_id')->nullable();
            $table->unsignedInteger('instructor_id')->nullable();
            $table->unsignedInteger('subject_id')->nullable();
            $table->unsignedInteger('school_year')->nullable();
            $table->string('term', 20)->nullable();
            $table->unsignedInteger('question_template_id')->nullable();
            $table->unsignedInteger('evaluator_id')->nullable();
            $table->unsignedInteger('evaluator_type')->nullable();
            $table->unsignedInteger('status')->nullable();
            $table->decimal('average', 5, 2)->nullable();
            $table->string('rating', 20)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_user_id')->nullable();
            $table->integer('updated_user_id')->nullable();
            $table->integer('deleted_user_id')->nullable();
            // Foreign keys can be added after all referenced tables exist
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};

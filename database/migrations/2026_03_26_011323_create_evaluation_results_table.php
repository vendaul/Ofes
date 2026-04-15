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
        Schema::create('evaluation_results', function (Blueprint $table) {
            $table->id();
            // Legacy assignment id kept nullable for backward compatibility.
            $table->unsignedBigInteger('asign_id')->nullable();
            $table->unsignedInteger('class_schedule_id')->nullable();
            $table->integer('total_evaluations')->default(0);
            $table->decimal('overall_average', 3, 2)->nullable();
            $table->string('overall_rating', 20)->nullable();
            $table->json('category_averages')->nullable(); // Store category averages as JSON
            $table->json('question_averages')->nullable(); // Store individual question averages as JSON
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();

            $table->foreign('class_schedule_id')->references('id')->on('db_class_schedules')->onDelete('cascade');
            $table->unique('class_schedule_id'); // One result per class schedule
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_results');
    }
};

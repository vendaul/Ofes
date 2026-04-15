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
        Schema::create('student_otps', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_id');
            $table->string('otp_code', 6);
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('db_students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_otps');
    }
};

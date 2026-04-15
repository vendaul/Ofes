<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('question_templates', function (Blueprint $table) {
            // date when the template was created/issued
            $table->date('template_date')->nullable()->after('description');
            $table->string('semester')->nullable()->after('template_date');
            $table->string('school_year')->nullable()->after('semester');
        });
    }

    public function down(): void
    {
        Schema::table('question_templates', function (Blueprint $table) {
            $table->dropColumn(['template_date', 'semester', 'school_year']);
        });
    }
};

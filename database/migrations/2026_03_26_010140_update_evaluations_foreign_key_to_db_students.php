<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update evaluation records to use db_students.id instead of students.student_id
        DB::statement('
            UPDATE evaluations e
            INNER JOIN students s ON e.student_id = s.student_id
            INNER JOIN db_students ds ON s.user_id = ds.account_id
            SET e.student_id = ds.id
        ');

        // Drop the existing foreign key constraint if exists (idempotent)
        $keys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'evaluations' AND COLUMN_NAME = 'student_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
        foreach ($keys as $key) {
            DB::statement("ALTER TABLE evaluations DROP FOREIGN KEY `{$key->CONSTRAINT_NAME}`");
        }
    
        // Ensure same column types for foreign key
        DB::statement('ALTER TABLE evaluations MODIFY student_id INT(11) NOT NULL');

        // Add new foreign key constraint to db_students table
        DB::statement('ALTER TABLE evaluations ADD CONSTRAINT evaluations_ibfk_1 FOREIGN KEY (student_id) REFERENCES db_students(id) ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new foreign key constraint
        DB::statement('ALTER TABLE evaluations DROP FOREIGN KEY evaluations_ibfk_1');

        // Convert back to students.student_id
        DB::statement('
            UPDATE evaluations e
            INNER JOIN db_students ds ON e.student_id = ds.id
            INNER JOIN students s ON ds.account_id = s.user_id
            SET e.student_id = s.student_id
        ');

        // Add back the original foreign key constraint to students table
        DB::statement('ALTER TABLE evaluations ADD CONSTRAINT evaluations_ibfk_1 FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE');
    }
};

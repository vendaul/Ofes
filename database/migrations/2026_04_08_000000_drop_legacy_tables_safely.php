<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Move any remaining legacy students.student_id values to db_students.id before dropping the table.
        if (Schema::hasTable('evaluations') && Schema::hasTable('students') && Schema::hasTable('db_students')) {
            DB::statement(
                'UPDATE evaluations e
                 INNER JOIN students s ON e.student_id = s.student_id
                 INNER JOIN db_students ds ON ds.account_id = s.user_id
                 SET e.student_id = ds.id'
            );
        }

        $this->dropForeignKeysFor('evaluations', ['student_id', 'asign_id']);
        $this->dropForeignKeysFor('evaluation_results', ['asign_id']);

        // Keep legacy column but detach old values from removed table.
        if (Schema::hasTable('evaluations') && Schema::hasColumn('evaluations', 'asign_id')) {
            DB::statement('UPDATE evaluations SET asign_id = NULL WHERE asign_id IS NOT NULL');
        }

        if (Schema::hasTable('evaluation_results') && Schema::hasColumn('evaluation_results', 'asign_id')) {
            DB::statement('UPDATE evaluation_results SET asign_id = NULL WHERE asign_id IS NOT NULL');
        }

        // Drop deprecated tables only after all known and unknown incoming foreign keys are removed.
        foreach (['student_accounts', 'menu', 'instructors', 'sections', 'subjects', 'teaching_assignments', 'students'] as $table) {
            $this->dropIncomingForeignKeysTo($table);
            Schema::dropIfExists($table);
        }
    }

    public function down(): void
    {
        // Intentionally irreversible: this migration removes deprecated tables and legacy link data.
    }

    private function dropForeignKeysFor(string $table, array $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                continue;
            }

            $keys = DB::select(
                "SELECT CONSTRAINT_NAME
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND COLUMN_NAME = ?
                   AND REFERENCED_TABLE_NAME IS NOT NULL",
                [$table, $column]
            );

            foreach ($keys as $key) {
                DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY `{$key->CONSTRAINT_NAME}`");
            }
        }
    }

    private function dropIncomingForeignKeysTo(string $referencedTable): void
    {
        if (!Schema::hasTable($referencedTable)) {
            return;
        }

        $incoming = DB::select(
            "SELECT TABLE_NAME, CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND REFERENCED_TABLE_NAME = ?",
            [$referencedTable]
        );

        foreach ($incoming as $fk) {
            if (!Schema::hasTable($fk->TABLE_NAME)) {
                continue;
            }

            DB::statement("ALTER TABLE {$fk->TABLE_NAME} DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }
    }
};

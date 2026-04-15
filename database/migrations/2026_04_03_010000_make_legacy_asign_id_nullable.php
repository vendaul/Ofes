<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->makeNullableIfPresent('evaluations');
        $this->makeNullableIfPresent('evaluation_results');
    }

    public function down(): void
    {
        // Intentionally left irreversible because null asign_id values may exist after this migration.
    }

    private function makeNullableIfPresent(string $table): void
    {
        if (!Schema::hasColumn($table, 'asign_id')) {
            return;
        }

        $column = DB::selectOne("SHOW COLUMNS FROM {$table} LIKE 'asign_id'");

        if (!$column || $column->Null === 'YES') {
            return;
        }

        DB::statement("ALTER TABLE {$table} MODIFY asign_id BIGINT UNSIGNED NULL");
    }
};
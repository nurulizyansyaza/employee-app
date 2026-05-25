<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds indexes required for acceptable query performance at 100 000+ rows.
 *
 * Existing indexes (from 2024_06_02 migration): name, birthdate, salary.
 *
 * New indexes added here:
 *  - is_active              → fast filtering by active/inactive status
 *  - currency               → fast filtering/grouping by currency
 *  - (is_active, name)      → covering index for the default list view
 *                             (active employees sorted by name)
 *  - (is_active, salary)    → covering index for salary-sorted filtered list
 *
 * PostgreSQL text-search note:
 *  For LIKE '%term%' searches on name/nik/address the query planner cannot
 *  use a standard B-tree index. Install the pg_trgm extension and add GIN
 *  indexes when full-text search performance becomes critical:
 *
 *    DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
 *    DB::statement('CREATE INDEX employees_name_trgm ON employees USING GIN (name gin_trgm_ops)');
 *    DB::statement('CREATE INDEX employees_nik_trgm  ON employees USING GIN (nik  gin_trgm_ops)');
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->index('is_active', 'employees_is_active_index');
            $table->index('currency',  'employees_currency_index');
            $table->index(['is_active', 'name'],   'employees_is_active_name_index');
            $table->index(['is_active', 'salary'], 'employees_is_active_salary_index');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('employees_is_active_name_index');
            $table->dropIndex('employees_is_active_salary_index');
            $table->dropIndex('employees_is_active_index');
            $table->dropIndex('employees_currency_index');
        });
    }
};

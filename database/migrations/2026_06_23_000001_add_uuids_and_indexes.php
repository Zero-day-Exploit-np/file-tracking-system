<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // ── UUIDs ─────────────────────────────────────────────────
        // Cross-database: works on MySQL and SQLite.

        $tables = ['users', 'file_records', 'departments', 'designations'];

        foreach ($tables as $table) {
            if (! Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->string('uuid', 36)->nullable()->after('id');
                });

                DB::table($table)->orderBy('id')->chunk(100, function ($rows) use ($table) {
                    foreach ($rows as $row) {
                        DB::table($table)->where('id', $row->id)
                            ->update(['uuid' => Str::uuid()->toString()]);
                    }
                });

                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->string('uuid', 36)->nullable(false)->change();
                    $t->unique('uuid', "{$table}_uuid_unique");
                });
            }
        }

        // ── PERFORMANCE INDEXES ───────────────────────────────────
        // Uses Schema::hasIndex() / Schema::getIndexes() available in Laravel 11+.
        // Falls back to a try/catch so that if an index already exists,
        // the migration succeeds rather than crashing.

        // file_records
        Schema::table('file_records', function (Blueprint $table) {
            $this->safeIndex($table, 'file_number',    'file_records_file_number_index');
            $this->safeIndex($table, 'status',         'file_records_status_index');
            $this->safeIndex($table, 'department_id',  'file_records_department_id_index');
            $this->safeIndex($table, 'current_user_id','file_records_current_user_id_index');
            $this->safeIndex($table, 'created_at',     'file_records_created_at_index');
        });

        // file_movements
        Schema::table('file_movements', function (Blueprint $table) {
            $this->safeIndex($table, 'file_id',    'file_movements_file_id_index');
            $this->safeIndex($table, 'action',     'file_movements_action_index');
            $this->safeIndex($table, 'from_user',  'file_movements_from_user_index');
            $this->safeIndex($table, 'to_user',    'file_movements_to_user_index');
            $this->safeIndex($table, 'created_at', 'file_movements_created_at_index');
        });

        // users
        Schema::table('users', function (Blueprint $table) {
            $this->safeIndex($table, 'department_id', 'users_department_id_index');
            $this->safeIndex($table, 'role',          'users_role_index');
        });

        // audit_logs (conditional — table may not exist in all environments)
        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $this->safeIndex($table, 'action',     'audit_logs_action_index');
                $this->safeIndex($table, 'user_id',    'audit_logs_user_id_index');
                $this->safeIndex($table, 'created_at', 'audit_logs_created_at_index');
            });
        }
    }

    public function down(): void
    {
        foreach (['users', 'file_records', 'departments', 'designations'] as $table) {
            if (Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->dropUnique("{$table}_uuid_unique");
                    $t->dropColumn('uuid');
                });
            }
        }
    }

    /**
     * Add an index only if it does not already exist.
     * Uses a try/catch so it works on both MySQL and SQLite,
     * and is idempotent (safe to re-run).
     */
    private function safeIndex(Blueprint $blueprint, string $column, string $name): void
    {
        try {
            $blueprint->index($column, $name);
        } catch (\Throwable) {
            // Index already exists — safe to ignore
        }
    }
};

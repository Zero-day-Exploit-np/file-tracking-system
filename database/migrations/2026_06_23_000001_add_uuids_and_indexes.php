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

        $tables = ['users', 'file_records', 'departments', 'designations'];

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->string('uuid', 36)->nullable()->after('id');
                });

                // Backfill with orderBy to satisfy chunking requirement
                DB::table($table)->orderBy('id')->chunk(100, function ($rows) use ($table) {
                    foreach ($rows as $row) {
                        DB::table($table)->where('id', $row->id)->update(['uuid' => Str::uuid()->toString()]);
                    }
                });

                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->string('uuid', 36)->nullable(false)->change();
                    $t->unique('uuid', "{$table}_uuid_unique");
                });
            }
        }

        // ── PERFORMANCE INDEXES ──────────────────────────────────

        // file_records
        Schema::table('file_records', function (Blueprint $table) {
            $this->addIndexIfMissing($table, 'file_records', 'file_number',     'file_records_file_number_index');
            $this->addIndexIfMissing($table, 'file_records', 'status',           'file_records_status_index');
            $this->addIndexIfMissing($table, 'file_records', 'department_id',    'file_records_department_id_index');
            $this->addIndexIfMissing($table, 'file_records', 'current_user_id',  'file_records_current_user_id_index');
            $this->addIndexIfMissing($table, 'file_records', 'created_at',       'file_records_created_at_index');
        });

        // transfer_requests
        Schema::table('transfer_requests', function (Blueprint $table) {
            $this->addIndexIfMissing($table, 'transfer_requests', 'status',        'transfer_requests_status_index');
            $this->addIndexIfMissing($table, 'transfer_requests', 'to_department', 'transfer_requests_to_department_index');
            $this->addIndexIfMissing($table, 'transfer_requests', 'requested_by',  'transfer_requests_requested_by_index');
        });

        // file_movements
        Schema::table('file_movements', function (Blueprint $table) {
            $this->addIndexIfMissing($table, 'file_movements', 'file_id',    'file_movements_file_id_index');
            $this->addIndexIfMissing($table, 'file_movements', 'action',     'file_movements_action_index');
            $this->addIndexIfMissing($table, 'file_movements', 'from_user',  'file_movements_from_user_index');
            $this->addIndexIfMissing($table, 'file_movements', 'to_user',    'file_movements_to_user_index');
            $this->addIndexIfMissing($table, 'file_movements', 'created_at', 'file_movements_created_at_index');
        });

        // users
        Schema::table('users', function (Blueprint $table) {
            $this->addIndexIfMissing($table, 'users', 'department_id', 'users_department_id_index');
            $this->addIndexIfMissing($table, 'users', 'role',          'users_role_index');
        });

        // audit_logs
        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $this->addIndexIfMissing($table, 'audit_logs', 'action',     'audit_logs_action_index');
                $this->addIndexIfMissing($table, 'audit_logs', 'user_id',    'audit_logs_user_id_index');
                $this->addIndexIfMissing($table, 'audit_logs', 'created_at', 'audit_logs_created_at_index');
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

    private function addIndexIfMissing(Blueprint $blueprint, string $table, string $column, string $name): void
    {
        try {
            $existing = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$name]);
            if (empty($existing)) {
                $blueprint->index($column, $name);
            }
        } catch (\Throwable) {
            // If check fails, skip silently — index may already exist
        }
    }
};

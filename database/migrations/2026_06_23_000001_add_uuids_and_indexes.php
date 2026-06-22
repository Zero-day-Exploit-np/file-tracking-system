<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // ── UUIDs ─────────────────────────────────────────────────

        // Users
        if (!Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id');
                $table->index('uuid');
            });
            \DB::table('users')->each(fn($r) => \DB::table('users')->where('id', $r->id)->update(['uuid' => Str::uuid()]));
            Schema::table('users', function (Blueprint $table) {
                $table->uuid('uuid')->nullable(false)->change();
            });
        }

        // file_records
        if (!Schema::hasColumn('file_records', 'uuid')) {
            Schema::table('file_records', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id');
                $table->index('uuid');
            });
            \DB::table('file_records')->each(fn($r) => \DB::table('file_records')->where('id', $r->id)->update(['uuid' => Str::uuid()]));
            Schema::table('file_records', function (Blueprint $table) {
                $table->uuid('uuid')->nullable(false)->change();
            });
        }

        // departments
        if (!Schema::hasColumn('departments', 'uuid')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id');
                $table->index('uuid');
            });
            \DB::table('departments')->each(fn($r) => \DB::table('departments')->where('id', $r->id)->update(['uuid' => Str::uuid()]));
            Schema::table('departments', function (Blueprint $table) {
                $table->uuid('uuid')->nullable(false)->change();
            });
        }

        // designations
        if (!Schema::hasColumn('designations', 'uuid')) {
            Schema::table('designations', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id');
                $table->index('uuid');
            });
            \DB::table('designations')->each(fn($r) => \DB::table('designations')->where('id', $r->id)->update(['uuid' => Str::uuid()]));
            Schema::table('designations', function (Blueprint $table) {
                $table->uuid('uuid')->nullable(false)->change();
            });
        }

        // ── PERFORMANCE INDEXES ──────────────────────────────────

        // file_records
        Schema::table('file_records', function (Blueprint $table) {
            if (!$this->hasIndex('file_records', 'file_records_file_number_index')) {
                $table->index('file_number');
            }
            if (!$this->hasIndex('file_records', 'file_records_status_index')) {
                $table->index('status');
            }
            if (!$this->hasIndex('file_records', 'file_records_department_id_index')) {
                $table->index('department_id');
            }
            if (!$this->hasIndex('file_records', 'file_records_current_user_id_index')) {
                $table->index('current_user_id');
            }
            if (!$this->hasIndex('file_records', 'file_records_created_at_index')) {
                $table->index('created_at');
            }
        });

        // transfer_requests
        Schema::table('transfer_requests', function (Blueprint $table) {
            if (!$this->hasIndex('transfer_requests', 'transfer_requests_status_index')) {
                $table->index('status');
            }
            if (!$this->hasIndex('transfer_requests', 'transfer_requests_to_department_index')) {
                $table->index('to_department');
            }
            if (!$this->hasIndex('transfer_requests', 'transfer_requests_requested_by_index')) {
                $table->index('requested_by');
            }
        });

        // file_movements
        Schema::table('file_movements', function (Blueprint $table) {
            if (!$this->hasIndex('file_movements', 'file_movements_file_id_index')) {
                $table->index('file_id');
            }
            if (!$this->hasIndex('file_movements', 'file_movements_action_index')) {
                $table->index('action');
            }
            if (!$this->hasIndex('file_movements', 'file_movements_from_user_index')) {
                $table->index('from_user');
            }
            if (!$this->hasIndex('file_movements', 'file_movements_to_user_index')) {
                $table->index('to_user');
            }
            if (!$this->hasIndex('file_movements', 'file_movements_created_at_index')) {
                $table->index('created_at');
            }
        });

        // users
        Schema::table('users', function (Blueprint $table) {
            if (!$this->hasIndex('users', 'users_department_id_index')) {
                $table->index('department_id');
            }
            if (!$this->hasIndex('users', 'users_role_index')) {
                $table->index('role');
            }
        });

        // audit_logs
        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                if (!$this->hasIndex('audit_logs', 'audit_logs_action_index')) {
                    $table->index('action');
                }
                if (!$this->hasIndex('audit_logs', 'audit_logs_user_id_index')) {
                    $table->index('user_id');
                }
                if (!$this->hasIndex('audit_logs', 'audit_logs_created_at_index')) {
                    $table->index('created_at');
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['users', 'file_records', 'departments', 'designations'] as $table) {
            if (Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, fn(Blueprint $t) => $t->dropColumn('uuid'));
            }
        }
    }

    private function hasIndex(string $table, string $index): bool
    {
        try {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes($table);
            return isset($indexes[$index]);
        } catch (\Throwable) {
            return false;
        }
    }
};

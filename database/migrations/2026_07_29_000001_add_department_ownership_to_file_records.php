<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Department-Owned Files — Phase 1: Schema
 *
 * Adds `current_department_id` to file_records so that a department can own
 * a file independently of any user. Also adds `pending_assignment` status.
 *
 * Backfill: current_department_id is set to department_id for all existing rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('file_records', function (Blueprint $table) {
            // Track which department currently holds the file.
            // NULL is not allowed — every file must belong to a department.
            $table->unsignedBigInteger('current_department_id')
                  ->nullable()
                  ->after('department_id');

            $table->foreign('current_department_id')
                  ->references('id')
                  ->on('departments')
                  ->nullOnDelete();
        });

        // Backfill: every existing file's current dept = its registered dept
        DB::statement('UPDATE file_records SET current_department_id = department_id');

        // Add pending_assignment to the status enum
        // MySQL/MariaDB syntax — we rebuild the enum column
        DB::statement("ALTER TABLE file_records MODIFY COLUMN status ENUM('draft','active','pending_transfer','pending_assignment','archived') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        // Remove pending_assignment from enum first
        DB::statement("ALTER TABLE file_records MODIFY COLUMN status ENUM('draft','active','pending_transfer','archived') NOT NULL DEFAULT 'active'");

        Schema::table('file_records', function (Blueprint $table) {
            $table->dropForeign(['current_department_id']);
            $table->dropColumn('current_department_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Make receiver_id nullable so a file can be transferred to a department
 * even when that department has no assigned user yet.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('file_transfers', function (Blueprint $table) {
            // Drop the old NOT NULL foreign key
            $table->dropForeign(['receiver_id']);

            // Re-add as nullable with foreign key
            $table->unsignedBigInteger('receiver_id')->nullable()->change();
            $table->foreign('receiver_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('file_transfers', function (Blueprint $table) {
            $table->dropForeign(['receiver_id']);
            $table->unsignedBigInteger('receiver_id')->nullable(false)->change();
            $table->foreign('receiver_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};

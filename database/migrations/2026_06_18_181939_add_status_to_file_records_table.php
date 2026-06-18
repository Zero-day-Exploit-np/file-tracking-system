<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('file_records', function ($table) {
            $table->enum('status', [
                'draft',
                'active',
                'pending_transfer',
                'archived'
            ])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_records', function (Blueprint $table) {
            //
        });
    }
};

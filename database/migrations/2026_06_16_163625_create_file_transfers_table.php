<?php

 use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('file_transfers', function (Blueprint $table) {
            $table->id();

            // File being transferred
            $table->foreignId('file_record_id')     // file_id
                ->constrained('file_records')       // files
                ->onDelete('cascade');

            // Transfer tracking
            $table->foreignId('from_user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('to_user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Department tracking (important for SRS)
            $table->foreignId('from_department_id')
                ->constrained('departments')
                ->onDelete('cascade');

            $table->foreignId('to_department_id')
                ->constrained('departments')
                ->onDelete('cascade');

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_transfers');
    }
};
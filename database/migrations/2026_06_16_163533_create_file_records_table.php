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
        Schema::create('files', function (Blueprint $table) {

            $table->id();

            $table->string('file_number')->unique();

            $table->string('file_name');

            $table->text('remarks')->nullable();

            $table->date('creation_date');

            $table->enum('status', [
                'created',
                'pending',
                'in_progress',
                'approved',
                'rejected',
                'closed'
            ])->default('created');

            $table->foreignId('created_by');

            $table->foreignId('department_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};

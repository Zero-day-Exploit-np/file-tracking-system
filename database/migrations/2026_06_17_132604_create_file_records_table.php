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
        Schema::create('file_records', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('created_by')->nullable();

            $table->string('file_name');
            $table->string('file_number')->unique();

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_records');
    }
};

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
        Schema::create('file_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_id');

            $table->unsignedBigInteger('from_user')->nullable();
            $table->unsignedBigInteger('to_user')->nullable();

            $table->unsignedBigInteger('from_department')->nullable();
            $table->unsignedBigInteger('to_department')->nullable();

            $table->string('action'); // created, transfer, approved, rejected
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_movements');
    }
};

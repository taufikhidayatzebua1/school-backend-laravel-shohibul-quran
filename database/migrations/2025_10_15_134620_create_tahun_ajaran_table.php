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
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->string('semester'); // Bisa: Ganjil, Genap, Semester 1, Semester 2, Q1, Q2, etc
            $table->string('tahun'); // Format: 2024/2025, 2025, etc
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            // Ensure only one active tahun_ajaran
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');
    }
};

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
        Schema::create('hafalan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->onDelete('set null');
            $table->foreignId('guru_id')->nullable()->constrained('guru')->onDelete('set null');
            $table->integer('surah_id');
            $table->integer('ayat_dari');
            $table->integer('ayat_sampai');
            $table->enum('status', ['lancar', 'perlu_bimbingan', 'mengulang'])->default('lancar');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hafalan');
    }
};

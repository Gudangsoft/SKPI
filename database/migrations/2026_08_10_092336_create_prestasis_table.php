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
        Schema::create('prestasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_skpi_id')->constrained('pengajuan_skpis')->cascadeOnDelete();
            $table->string('nama_prestasi');
            $table->string('tingkat');
            $table->string('peringkat')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->unsignedSmallInteger('tahun');
            $table->string('dokumen_bukti_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasis');
    }
};

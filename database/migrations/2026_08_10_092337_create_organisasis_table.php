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
        Schema::create('organisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_skpi_id')->constrained('pengajuan_skpis')->cascadeOnDelete();
            $table->string('nama_organisasi');
            $table->string('jabatan');
            $table->date('periode_mulai');
            $table->date('periode_selesai')->nullable();
            $table->string('dokumen_bukti_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisasis');
    }
};

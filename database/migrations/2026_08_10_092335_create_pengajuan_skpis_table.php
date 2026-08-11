<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_skpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->string('status')->default('draft')->index();

            // Data Akademik snapshot for this submission.
            $table->decimal('ipk', 3, 2)->nullable();
            $table->string('judul_skripsi')->nullable();
            $table->date('tanggal_lulus')->nullable();
            $table->string('no_ijazah')->nullable();
            $table->string('predikat_kelulusan')->nullable();

            $table->text('catatan_revisi')->nullable();

            $table->timestamp('diajukan_at')->nullable();
            $table->foreignId('diverifikasi_prodi_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_prodi_at')->nullable();
            $table->foreignId('disetujui_fakultas_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_fakultas_at')->nullable();

            $table->foreignId('pejabat_penandatangan_id')->nullable()->constrained('pejabat_penandatangans')->nullOnDelete();
            $table->string('nomor_skpi')->nullable()->unique();
            $table->timestamp('nomor_skpi_generated_at')->nullable();
            $table->string('pdf_path')->nullable();

            $table->uuid('verification_token')->unique()->default(new Expression('(uuid())'));
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_skpis');
    }
};

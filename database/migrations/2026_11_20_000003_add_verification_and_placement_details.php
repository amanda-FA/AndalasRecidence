<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pendaftaran_hunian', function (Blueprint $table) {
            $table->dateTime('tanggal_daftar')->change();
            $table->dateTime('tanggal_verifikasi')->nullable()->change();
        });
        Schema::table('penempatan_kamar', function (Blueprint $table) {
            $table->unsignedSmallInteger('nomor_bed')->nullable();
            $table->date('rencana_keluar')->nullable();
            $table->dateTime('jadwal_kunci_mulai')->nullable();
            $table->dateTime('jadwal_kunci_selesai')->nullable();
            $table->string('lokasi_kunci')->nullable();
            $table->string('penanggung_jawab', 150)->nullable();
            $table->string('kontak_penanggung_jawab', 30)->nullable();
            $table->index(['id_kamar', 'status_penempatan', 'tanggal_masuk'], 'idx_penempatan_ketersediaan');
        });
    }

    public function down(): void
    {
        Schema::table('penempatan_kamar', function (Blueprint $table) {
            $table->dropIndex('idx_penempatan_ketersediaan');
            $table->dropColumn(['nomor_bed', 'rencana_keluar', 'jadwal_kunci_mulai', 'jadwal_kunci_selesai', 'lokasi_kunci', 'penanggung_jawab', 'kontak_penanggung_jawab']);
        });
        Schema::table('pendaftaran_hunian', function (Blueprint $table) {
            $table->date('tanggal_daftar')->change();
            $table->date('tanggal_verifikasi')->nullable()->change();
        });
    }
};

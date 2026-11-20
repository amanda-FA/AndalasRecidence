<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pendaftaran_hunian', function (Blueprint $table) {
            $table->date('tanggal_masuk')->nullable();
            $table->date('rencana_keluar')->nullable();
            $table->dateTime('jadwal_kunci_mulai')->nullable();
            $table->dateTime('jadwal_kunci_selesai')->nullable();
            $table->string('lokasi_kunci')->nullable();
            $table->string('penanggung_jawab', 150)->nullable();
            $table->string('kontak_penanggung_jawab', 30)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_hunian', function (Blueprint $table) {
            $table->dropColumn(['tanggal_masuk', 'rencana_keluar', 'jadwal_kunci_mulai', 'jadwal_kunci_selesai', 'lokasi_kunci', 'penanggung_jawab', 'kontak_penanggung_jawab']);
        });
    }
};

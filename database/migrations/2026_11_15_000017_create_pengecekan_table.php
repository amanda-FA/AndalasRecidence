<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengecekan', function (Blueprint $table) {
            $table->increments('id_pengecekan');
            $table->unsignedInteger('id_pengguna');
            $table->unsignedInteger('id_pengajuan')->unique('uq_pengecekan_pengajuan');
            $table->date('tanggal_cek');
            $table->string('kondisi_kamar', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->string('status_verifikasi', 30);

            $table->foreign('id_pengguna', 'fk_pengecekan_pengguna')
                ->references('id_pengguna')->on('pengguna')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_pengajuan', 'fk_pengecekan_pengajuan')
                ->references('id_pengajuan')->on('pengajuan_checkout')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengecekan');
    }
};

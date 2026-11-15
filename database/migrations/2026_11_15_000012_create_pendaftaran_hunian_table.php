<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_hunian', function (Blueprint $table) {
            $table->increments('id_pendaftaran');
            $table->unsignedInteger('id_client');
            $table->unsignedInteger('id_periode');
            $table->unsignedInteger('id_tipeKamar');
            $table->unsignedInteger('id_admin')->nullable();
            $table->date('tanggal_daftar');
            $table->string('status_pendaftaran', 30);
            $table->date('tanggal_verifikasi')->nullable();
            $table->text('catatan_verifikasi')->nullable();

            $table->foreign('id_client', 'fk_pendaftaran_client')
                ->references('id_client')->on('client')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_periode', 'fk_pendaftaran_periode')
                ->references('id_periode')->on('periode_pendaftaran')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_tipeKamar', 'fk_pendaftaran_tipe')
                ->references('id_tipeKamar')->on('tipe_kamar')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_admin', 'fk_pendaftaran_admin')
                ->references('id_pengguna')->on('pengguna')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_hunian');
    }
};

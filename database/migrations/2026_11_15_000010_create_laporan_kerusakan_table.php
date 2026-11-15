<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_kerusakan', function (Blueprint $table) {
            $table->increments('id_laporan');
            $table->unsignedInteger('id_pengguna');
            $table->unsignedInteger('id_fasilitas');
            $table->text('deskripsi');
            $table->string('foto', 255)->nullable();
            $table->date('tanggal_lapor');
            $table->string('status_perbaikan', 30);
            $table->date('tanggal_selesai')->nullable();

            $table->foreign('id_pengguna', 'fk_laporan_pengguna')
                ->references('id_pengguna')->on('pengguna')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_fasilitas', 'fk_laporan_fasilitas')
                ->references('id_fasilitas')->on('fasilitas')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kerusakan');
    }
};

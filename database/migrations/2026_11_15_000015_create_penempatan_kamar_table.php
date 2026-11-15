<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penempatan_kamar', function (Blueprint $table) {
            $table->increments('id_penempatan');
            $table->unsignedInteger('id_pendaftaran');
            $table->unsignedInteger('id_kamar');
            $table->unsignedInteger('id_admin')->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->string('status_penempatan', 30);

            $table->foreign('id_pendaftaran', 'fk_penempatan_pendaftaran')
                ->references('id_pendaftaran')->on('pendaftaran_hunian')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_kamar', 'fk_penempatan_kamar')
                ->references('id_kamar')->on('kamar')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_admin', 'fk_penempatan_admin')
                ->references('id_pengguna')->on('pengguna')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penempatan_kamar');
    }
};

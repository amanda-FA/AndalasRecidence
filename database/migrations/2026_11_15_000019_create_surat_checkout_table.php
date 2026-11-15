<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_checkout', function (Blueprint $table) {
            $table->increments('id_surat');
            $table->unsignedInteger('id_pengajuan')->unique('uq_surat_checkout_pengajuan');
            $table->string('jenis_surat', 50);
            $table->string('nomor_surat', 100);
            $table->date('tanggal_terbit');
            $table->string('file_surat', 255)->nullable();

            $table->foreign('id_pengajuan', 'fk_surat_checkout_pengajuan')
                ->references('id_pengajuan')->on('pengajuan_checkout')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_checkout');
    }
};

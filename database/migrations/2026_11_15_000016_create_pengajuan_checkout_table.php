<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_checkout', function (Blueprint $table) {
            $table->increments('id_pengajuan');
            $table->unsignedInteger('id_penempatan')->unique('uq_pengajuan_penempatan');
            $table->unsignedInteger('id_fasilitator')->nullable();
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_rencanaKeluar');
            $table->text('alasan')->nullable();
            $table->string('status_pengajuan', 30);
            $table->date('tangal_verifikasi')->nullable();
            $table->text('catatan')->nullable();

            $table->foreign('id_penempatan', 'fk_pengajuan_penempatan')
                ->references('id_penempatan')->on('penempatan_kamar')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_fasilitator', 'fk_pengajuan_fasilitator')
                ->references('id_pengguna')->on('pengguna')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_checkout');
    }
};

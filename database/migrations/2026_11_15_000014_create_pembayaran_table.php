<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->increments('id_pembayaran');
            $table->unsignedInteger('id_pendaftaran');
            $table->decimal('jumlah', 12, 2);
            $table->string('metode_bayar', 50);
            $table->string('bukti_bayar', 255)->nullable();
            $table->date('tanggal_bayar')->nullable();
            $table->string('status_pembayaran', 30);

            $table->foreign('id_pendaftaran', 'fk_pembayaran_pendaftaran')
                ->references('id_pendaftaran')->on('pendaftaran_hunian')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};

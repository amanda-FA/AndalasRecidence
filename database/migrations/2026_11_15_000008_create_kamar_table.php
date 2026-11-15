<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamar', function (Blueprint $table) {
            $table->increments('id_kamar');
            $table->unsignedInteger('id_gedung');
            $table->unsignedInteger('id_tipeKamar');
            $table->string('nomor_kamar', 20);
            $table->unsignedInteger('lantai')->nullable();
            $table->string('status_kamar', 30);

            $table->foreign('id_gedung', 'fk_kamar_gedung')
                ->references('id_gedung')->on('gedung')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_tipeKamar', 'fk_kamar_tipe')
                ->references('id_tipeKamar')->on('tipe_kamar')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};

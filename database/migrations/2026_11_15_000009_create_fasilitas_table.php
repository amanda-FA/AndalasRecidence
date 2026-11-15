<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->increments('id_fasilitas');
            $table->unsignedInteger('id_gedung')->nullable();
            $table->unsignedInteger('id_kamar')->nullable();
            $table->string('nama_fasilitas', 100);
            $table->string('kondisi', 50)->nullable();

            $table->foreign('id_gedung', 'fk_fasilitas_gedung')
                ->references('id_gedung')->on('gedung')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_kamar', 'fk_fasilitas_kamar')
                ->references('id_kamar')->on('kamar')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasilitas');
    }
};

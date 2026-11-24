<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_hunian', function (Blueprint $table) {
            $table->increments('id_tarif');

            $table->unsignedInteger('id_gedung');
            $table->unsignedInteger('id_tipeKamar');

            $table->string('kategori_tarif', 30);
            $table->string('satuan_tarif', 30);
            $table->decimal('nominal', 12, 2);

            $table->foreign('id_gedung')
                ->references('id_gedung')
                ->on('gedung')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_tipeKamar')
                ->references('id_tipeKamar')
                ->on('tipe_kamar')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unique([
                'id_gedung',
                'id_tipeKamar',
                'kategori_tarif',
                'satuan_tarif',
            ], 'unique_tarif_hunian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_hunian');
    }
};
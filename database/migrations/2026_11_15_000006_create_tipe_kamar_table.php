<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipe_kamar', function (Blueprint $table) {
            $table->increments('id_tipeKamar');
            $table->string('nama_tipe', 100);
            $table->unsignedInteger('kapasitas');
            $table->decimal('tarif', 12, 2);
            $table->text('deskripsi')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipe_kamar');
    }
};

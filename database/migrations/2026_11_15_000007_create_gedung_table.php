<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gedung', function (Blueprint $table) {
            $table->increments('id_gedung');
            $table->string('nama_gedung', 100);
            $table->string('jenis_penghuni', 50)->nullable();
            $table->unsignedInteger('jumlah_lantai')->nullable();
            $table->text('keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gedung');
    }
};

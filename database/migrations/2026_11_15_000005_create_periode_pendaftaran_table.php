<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_pendaftaran', function (Blueprint $table) {
            $table->increments('id_periode');
            $table->string('nama_periode', 100);
            $table->string('tahun_akademik', 20);
            $table->date('tanggal_buka');
            $table->date('tanggal_tutup');
            $table->string('status', 30);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_pendaftaran');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan_perbaikan', function (Blueprint $table) {
            $table->increments('id_perbaikan');
            $table->unsignedInteger('id_pengguna');
            $table->unsignedInteger('id_laporan');
            $table->string('nomor_identitas', 50)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->tinyInteger('status_KIPK')->nullable();
            $table->text('alamat_asal')->nullable();

            $table->foreign('id_pengguna', 'fk_penugasan_pengguna')
                ->references('id_pengguna')->on('pengguna')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_laporan', 'fk_penugasan_laporan')
                ->references('id_laporan')->on('laporan_kerusakan')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_perbaikan');
    }
};

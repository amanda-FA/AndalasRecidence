<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client', function (Blueprint $table) {
            $table->increments('id_client');
            $table->unsignedInteger('id_jenis_client');
            $table->unsignedInteger('id_pengguna');
            $table->string('nomor_identitas', 50)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->tinyInteger('status_KIPK')->nullable();
            $table->text('alamat_asal')->nullable();

            $table->foreign('id_jenis_client', 'fk_client_jenis')
                ->references('id_jenis_client')->on('jenis_client')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_pengguna', 'fk_client_pengguna')
                ->references('id_pengguna')->on('pengguna')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client');
    }
};

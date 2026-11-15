<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->increments('id_pengguna');
            $table->unsignedInteger('id_role');
            $table->string('nama_lengkap', 150);
            $table->string('email', 150)->unique('uq_pengguna_email');
            $table->string('no_hp', 20)->nullable();
            $table->string('username', 50)->unique('uq_pengguna_username');
            $table->string('password', 255);
            $table->tinyInteger('status_aktif')->default(1);

            $table->foreign('id_role', 'fk_pengguna_role')
                ->references('id_role')->on('role')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gedung_jenis_client', function (Blueprint $table) {
            $table->unsignedInteger('id_gedung');
            $table->unsignedInteger('id_jenis_client');

            $table->primary(['id_gedung', 'id_jenis_client']);

            $table->foreign('id_gedung', 'fk_gjc_gedung')
                ->references('id_gedung')
                ->on('gedung')
                ->onDelete('cascade');

            $table->foreign('id_jenis_client', 'fk_gjc_jenis_client')
                ->references('id_jenis_client')
                ->on('jenis_client')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gedung_jenis_client');
    }
};

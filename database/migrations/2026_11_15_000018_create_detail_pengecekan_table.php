<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pengecekan', function (Blueprint $table) {
            $table->increments('id_detail');
            $table->unsignedInteger('id_pengecekan');
            $table->unsignedInteger('id_fasilitas');
            $table->string('kondisi', 50);
            $table->text('keterangan')->nullable();

            $table->foreign('id_pengecekan', 'fk_detail_pengecekan')
                ->references('id_pengecekan')->on('pengecekan')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('id_fasilitas', 'fk_detail_fasilitas')
                ->references('id_fasilitas')->on('fasilitas')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pengecekan');
    }
};

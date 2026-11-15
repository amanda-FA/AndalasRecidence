<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berkas_pendaftaran', function (Blueprint $table) {
            $table->increments('id_berkas');
            $table->unsignedInteger('id_pendaftaran');
            $table->string('path_file', 255);
            $table->date('tanggal_upload');

            $table->foreign('id_pendaftaran', 'fk_berkas_pendaftaran')
                ->references('id_pendaftaran')->on('pendaftaran_hunian')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berkas_pendaftaran');
    }
};

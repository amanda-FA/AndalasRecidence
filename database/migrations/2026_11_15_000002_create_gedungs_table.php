<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gedungs', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama', 100);
            $table->string('peruntukan', 30)->default('putra');
            $table->string('status', 30)->default('aktif');
            $table->string('klaster_wilayah')->nullable();
            $table->unsignedInteger('jumlah_lantai')->default(1);
            $table->unsignedInteger('estimasi_kamar')->default(0);
            $table->unsignedInteger('kapasitas_bed')->default(0);
            $table->json('fasilitas')->nullable();
            $table->string('foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gedungs');
    }
};

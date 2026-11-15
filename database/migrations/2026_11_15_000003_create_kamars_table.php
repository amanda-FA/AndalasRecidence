<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gedung_id')->constrained('gedungs')->cascadeOnDelete();
            $table->string('nomor_kamar', 30);
            $table->unsignedInteger('lantai')->default(1);
            $table->string('tipe_kamar', 50)->default('Reguler');
            $table->unsignedInteger('kapasitas_bed')->default(4);
            $table->unsignedInteger('bed_terisi')->default(0);
            $table->string('status', 30)->default('tersedia');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['gedung_id', 'nomor_kamar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};

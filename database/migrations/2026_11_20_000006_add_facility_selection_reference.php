<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->unsignedInteger('id_fasilitas_acuan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->foreign('id_fasilitas_acuan', 'fk_fasilitas_acuan')->references('id_fasilitas')->on('fasilitas')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->dropForeign('fk_fasilitas_acuan');
            $table->dropColumn(['id_fasilitas_acuan', 'aktif']);
        });
    }
};

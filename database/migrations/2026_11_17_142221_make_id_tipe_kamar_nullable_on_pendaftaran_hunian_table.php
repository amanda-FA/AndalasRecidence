<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftaran_hunian', function (Blueprint $table) {
            $table->unsignedInteger('id_tipeKamar')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        // Jangan mengubah kembali menjadi wajib selama ada nilai kosong.
        if (
            \Illuminate\Support\Facades\DB::table('pendaftaran_hunian')
                ->whereNull('id_tipeKamar')
                ->exists()
        ) {
            throw new \RuntimeException(
                'Rollback dibatalkan karena ada pendaftaran tanpa tipe kamar.'
            );
        }

        Schema::table('pendaftaran_hunian', function (Blueprint $table) {
            $table->unsignedInteger('id_tipeKamar')
                ->nullable(false)
                ->change();
        });
    }
};
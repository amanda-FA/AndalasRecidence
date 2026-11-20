<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pendaftaran_hunian', function (Blueprint $table) {
            $table->unsignedInteger('id_kamar')->nullable()->after('id_tipeKamar');
            $table->foreign('id_kamar', 'fk_pendaftaran_kamar')->references('id_kamar')->on('kamar')->onUpdate('cascade')->onDelete('restrict');
        });
        // Backfill only rooms that were actually recorded as placements.
        foreach (DB::table('penempatan_kamar')->orderBy('id_penempatan')->get(['id_pendaftaran', 'id_kamar']) as $placement) {
            DB::table('pendaftaran_hunian')->where('id_pendaftaran', $placement->id_pendaftaran)->update(['id_kamar' => $placement->id_kamar]);
        }
    }

    public function down(): void
    {
        Schema::table('pendaftaran_hunian', function (Blueprint $table) {
            $table->dropForeign('fk_pendaftaran_kamar');
            $table->dropColumn('id_kamar');
        });
    }
};

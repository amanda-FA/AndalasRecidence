<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_berkas', function (Blueprint $table) {
            $table->increments('id_jenis_berkas');
            $table->string('nama_berkas', 100)->unique();
        });

        Schema::table('berkas_pendaftaran', function (Blueprint $table) {
            // Preserve legacy documents whose category has not yet been identified.
            $table->unsignedInteger('id_jenis_berkas')->nullable()->after('id_pendaftaran');
            $table->foreign('id_jenis_berkas', 'fk_berkas_jenis_berkas')
                ->references('id_jenis_berkas')->on('jenis_berkas')
                ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('berkas_pendaftaran', function (Blueprint $table) {
            $table->dropForeign(Schema::getConnection()->getDriverName() === 'sqlite' ? ['id_jenis_berkas'] : 'fk_berkas_jenis_berkas');
            $table->dropColumn('id_jenis_berkas');
        });
        Schema::dropIfExists('jenis_berkas');
    }
};

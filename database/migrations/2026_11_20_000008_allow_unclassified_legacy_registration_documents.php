<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Also align installations which already applied the original NOT NULL migration.
        Schema::table('berkas_pendaftaran', function (Blueprint $table) {
            $table->unsignedInteger('id_jenis_berkas')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Keep legacy NULL values intact; the original migration removes this column
        // when rolling back the complete feature. Do not force an arbitrary category.
    }
};

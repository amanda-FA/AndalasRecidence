<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kamars', function (Blueprint $table) {
            $table->string('sayap', 60)->nullable()->after('lantai');
            $table->unsignedInteger('tarif')->default(0)->after('bed_terisi');
            $table->boolean('rfid_enabled')->default(true)->after('status');
            $table->json('fasilitas')->nullable()->after('rfid_enabled');
            $table->string('foto')->nullable()->after('fasilitas');
        });
    }

    public function down(): void
    {
        Schema::table('kamars', function (Blueprint $table) {
            $table->dropColumn(['sayap', 'tarif', 'rfid_enabled', 'fasilitas', 'foto']);
        });
    }
};

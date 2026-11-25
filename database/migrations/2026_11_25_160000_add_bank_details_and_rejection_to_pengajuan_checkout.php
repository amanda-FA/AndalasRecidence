<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_checkout', function (Blueprint $table) {
            $table->string('nama_bank', 50)->nullable()->after('alasan');
            $table->string('nomor_rekening', 50)->nullable()->after('nama_bank');
            $table->string('nama_pemilik_rekening', 100)->nullable()->after('nomor_rekening');
            $table->decimal('nominal_deposit', 12, 2)->default(50000.00)->after('nama_pemilik_rekening');
            $table->string('status_deposit', 30)->default('menunggu_pencairan')->after('nominal_deposit');
            $table->decimal('denda_kerusakan', 12, 2)->default(0.00)->after('status_deposit');
            $table->text('alasan_penolakan')->nullable()->after('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_checkout', function (Blueprint $table) {
            $table->dropColumn([
                'nama_bank',
                'nomor_rekening',
                'nama_pemilik_rekening',
                'nominal_deposit',
                'status_deposit',
                'denda_kerusakan',
                'alasan_penolakan',
            ]);
        });
    }
};

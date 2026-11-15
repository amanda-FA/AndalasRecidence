<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('applicant_category')->nullable()->after('name');
            $table->string('identity_number', 32)->nullable()->after('applicant_category');
            $table->string('phone', 25)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['applicant_category', 'identity_number', 'phone']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrars', function (Blueprint $table) {
            $table->string('api_endpoint')->nullable()->after('website');
            $table->string('api_key')->nullable()->after('api_endpoint');
            $table->string('supported_tlds')->nullable()->after('api_key');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('supported_tlds');
    });

    }

    public function down(): void
    {
        Schema::table('registrars', function (Blueprint $table) {
            $table->dropColumn(['api_endpoint', 'api_key', 'supported_tlds', 'status']);
        });
    }
};

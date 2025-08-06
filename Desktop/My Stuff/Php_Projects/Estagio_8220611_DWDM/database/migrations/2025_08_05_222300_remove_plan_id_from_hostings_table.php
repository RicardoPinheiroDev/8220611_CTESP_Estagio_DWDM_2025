<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hostings', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['plan_id']);
            // Then drop the column
            $table->dropColumn('plan_id');
        });
    }

    public function down(): void
    {
        Schema::table('hostings', function (Blueprint $table) {
            // Add back plan_id column if needed to rollback
            $table->uuid('plan_id')->after('domain_id');
            $table->foreign('plan_id')->references('id')->on('hosting_plans')->onDelete('cascade');
        });
    }
};
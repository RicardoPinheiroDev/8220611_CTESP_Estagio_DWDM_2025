<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hostings', function (Blueprint $table) {
            // Add new plan columns
            $table->string('plan_name')->after('domain_id')->nullable();
            $table->string('storage_limit', 50)->after('plan_name')->nullable();
            $table->decimal('plan_price', 10, 2)->after('storage_limit')->nullable();
            $table->text('plan_features')->after('plan_price')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('hostings', function (Blueprint $table) {
            $table->dropColumn(['plan_name', 'storage_limit', 'plan_price', 'plan_features']);
        });
    }
};
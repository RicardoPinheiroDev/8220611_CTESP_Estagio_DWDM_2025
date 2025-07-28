<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('financial_movements', 'bank_iban')) {
                $table->string('bank_iban')->nullable();
            }
            if (!Schema::hasColumn('financial_movements', 'account_holder')) {
                $table->string('account_holder')->nullable();
            }
            if (!Schema::hasColumn('financial_movements', 'mbway_phone')) {
                $table->string('mbway_phone')->nullable();
            }
            if (!Schema::hasColumn('financial_movements', 'mbway_reference')) {
                $table->string('mbway_reference')->nullable();
            }
            if (!Schema::hasColumn('financial_movements', 'paypal_email')) {
                $table->string('paypal_email')->nullable();
            }
            if (!Schema::hasColumn('financial_movements', 'paypal_transaction_id')) {
                $table->string('paypal_transaction_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('financial_movements', function (Blueprint $table) {
            $table->dropColumn([
                'bank_iban',
                'account_holder',
                'mbway_phone',
                'mbway_reference',
                'paypal_email',
                'paypal_transaction_id'
            ]);
        });
    }
};
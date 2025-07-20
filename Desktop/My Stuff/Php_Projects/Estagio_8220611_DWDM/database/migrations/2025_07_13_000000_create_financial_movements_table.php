<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['payment', 'adjustment', 'credit', 'refund'])->default('payment');
            $table->decimal('amount', 10, 2);
            $table->text('description');
            $table->string('payment_method')->nullable();
            $table->string('reference_number')->nullable();
            $table->decimal('balance_after', 10, 2)->nullable();
            $table->foreignUuid('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'created_at']);
            $table->index(['invoice_id']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_movements');
    }
};
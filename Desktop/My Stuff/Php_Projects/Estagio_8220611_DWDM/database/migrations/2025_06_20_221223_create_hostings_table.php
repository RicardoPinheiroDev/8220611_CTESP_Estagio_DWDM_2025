<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('hostings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id');
            $table->string('account_name');
            $table->uuid('domain_id')->nullable();
            $table->uuid('plan_id');
            $table->uuid('server_id');
            $table->dateTime('starts_at');
            $table->dateTime('expires_at');
            $table->string('status')->default('active');
            $table->string('payment_status')->default('pending');
            $table->decimal('next_renewal_price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('domain_id')->references('id')->on('domains')->onDelete('set null');
            $table->foreign('plan_id')->references('id')->on('hosting_plans')->onDelete('cascade');
            $table->foreign('server_id')->references('id')->on('servers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostings');
    }
};

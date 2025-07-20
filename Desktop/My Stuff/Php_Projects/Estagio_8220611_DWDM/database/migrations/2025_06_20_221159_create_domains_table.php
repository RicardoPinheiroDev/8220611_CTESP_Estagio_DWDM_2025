<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id');
            $table->string('name')->unique();
            $table->dateTime('registered_at')->nullable();
            $table->dateTime('expires_at');
            $table->uuid('registrar_id');
            $table->boolean('is_managed')->default(false);
            $table->uuid('server_id')->nullable();
            $table->string('status')->default('active');
            $table->string('payment_status')->default('pending');
            $table->decimal('next_renewal_price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('registrar_id')->references('id')->on('registrars')->onDelete('cascade');
            $table->foreign('server_id')->references('id')->on('servers')->onDelete('set null');
        });
       
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};

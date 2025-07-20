<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id');
            $table->string('title');
            $table->text('message');

            $table->enum('type', [
                'invoice_created', 'invoice_paid', 'invoice_overdue',
                'domain_created', 'domain_renewed', 'domain_expiring',
                'domain_expired', 'domain_active', 'domain_suspended',
                'hosting_created', 'hosting_upgraded', 'hosting_suspended',
                'hosting_active', 'hosting_terminated',
                'support_ticket_created', 'support_ticket_open', 'support_ticket_in_progress',
                'support_ticket_resolved', 'support_ticket_closed', 'support_ticket_priority_changed',
                'general'
            ]);

            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'read_at']);
            $table->index(['client_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

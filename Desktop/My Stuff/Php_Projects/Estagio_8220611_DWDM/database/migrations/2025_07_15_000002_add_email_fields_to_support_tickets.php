<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->string('ticket_number')->unique()->after('id');
            $table->string('client_email')->nullable()->after('client_id');
            $table->string('email_thread_id')->nullable()->after('client_email');
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn(['ticket_number', 'client_email', 'email_thread_id']);
        });
    }
};
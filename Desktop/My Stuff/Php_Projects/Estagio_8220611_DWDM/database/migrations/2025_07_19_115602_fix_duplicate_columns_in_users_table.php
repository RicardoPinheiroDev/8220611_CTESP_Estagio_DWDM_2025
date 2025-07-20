<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'status')) {
                $table->boolean('status')->default(true);
            }
            
            if (!Schema::hasColumn('users', 'type')) {
                $table->string('type')->default('admin');
            }
            
            if (!Schema::hasColumn('users', 'admin_access_granted')) {
                $table->boolean('admin_access_granted')->default(false);
            }
            
            if (!Schema::hasColumn('users', 'granted_by')) {
                $table->uuid('granted_by')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'granted_at')) {
                $table->timestamp('granted_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};

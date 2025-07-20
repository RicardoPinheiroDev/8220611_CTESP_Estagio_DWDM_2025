<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
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

       
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                if (!Schema::hasColumn('clients', 'user_id')) {
                    $table->foreignUuid('user_id')
                        ->nullable()
                        ->constrained('users')
                        ->onDelete('cascade');
                }
            });
        }
    }

    public function down(): void
    {
       
        if (Schema::hasTable('clients') && Schema::hasColumn('clients', 'user_id')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'status', 
                'type', 
                'admin_access_granted', 
                'granted_by', 
                'granted_at'
            ]);
        });
    }
};
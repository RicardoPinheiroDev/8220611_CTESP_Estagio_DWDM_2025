<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up(): void
    {
       
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('status')->default(true);
            $table->string('type')->default('admin');
            $table->boolean('admin_access_granted')->default(false);
            $table->uuid('granted_by')->nullable();
            $table->timestamp('granted_at')->nullable();
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
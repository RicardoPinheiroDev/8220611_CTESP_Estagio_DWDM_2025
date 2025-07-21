<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'status')) {
                $table->boolean('status')->default(true);
            }
            if (!Schema::hasColumn('users', 'type')) {
                $table->enum('type', ['admin', 'employee', 'client'])->default('admin');
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
            $clients = DB::table('clients')->get();
            
            foreach ($clients as $client) {
                $existingUser = DB::table('users')->where('id', $client->id)->first();
                
                if (!$existingUser) {
                    DB::table('users')->insert([
                        'id' => $client->id,
                        'name' => $client->name,
                        'email' => $client->email,
                        'password' => $client->password,
                        'status' => $client->is_active ?? true,
                        'type' => 'client',
                        'created_at' => $client->created_at,
                        'updated_at' => $client->updated_at,
                    ]);
                } else {
                    DB::table('users')
                        ->where('id', $client->id)
                        ->update([
                            'type' => 'client',
                            'status' => $client->is_active ?? true,
                        ]);
                }
            }

            Schema::table('clients', function (Blueprint $table) {
                if (!Schema::hasColumn('clients', 'user_id')) {
                    $table->foreignUuid('user_id')
                        ->nullable()
                        ->constrained('users')
                        ->onDelete('cascade');
                }
            });
        }

        if (Schema::hasTable('teams')) {
            $teams = DB::table('teams')->get();
            
            foreach ($teams as $team) {
                $existingUser = DB::table('users')->where('id', $team->id)->first();
                
                if (!$existingUser) {
                    DB::table('users')->insert([
                        'id' => $team->id,
                        'name' => $team->name,
                        'email' => $team->email,
                        'password' => $team->password,
                        'status' => $team->status ?? true,
                        'type' => 'employee',
                        'created_at' => $team->created_at,
                        'updated_at' => $team->updated_at,
                    ]);
                } else {
                    DB::table('users')
                        ->where('id', $team->id)
                        ->update([
                            'type' => 'employee',
                            'status' => $team->status ?? true,
                        ]);
                }
            }
        }

        DB::table('users')
            ->whereNull('type')
            ->orWhere('type', '')
            ->update(['type' => 'admin', 'status' => true]);
    }

    public function down(): void
    {
        DB::table('users')->whereIn('type', ['client', 'employee'])->delete();
        
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
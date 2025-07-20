<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\User;
use App\Enums\UserType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class LinkClientsToUsers extends Command
{
    protected $signature = 'clients:link-to-users';
    protected $description = 'Link existing clients to users';

    public function handle()
    {
        $clients = Client::whereNull('user_id')->get();
        
        $this->info("Linking " . $clients->count() . " clients to users...");
        
        foreach ($clients as $client) {
            $user = User::updateOrCreate(
                ['email' => $client->email],
                [
                    'name' => $client->name,
                    'password' => $client->password,
                    'type' => UserType::CLIENT,
                    'status' => $client->is_active,
                ]
            );
            
            $client->update(['user_id' => $user->id]);
            $this->line("Linked client: " . $client->name);
        }
        
        $this->info('clients have been linked to users');
    }
}

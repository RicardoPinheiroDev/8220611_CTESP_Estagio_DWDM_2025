<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Initialize database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database/database.sqlite',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    // Clear all users
    $deletedUsers = Capsule::table('users')->delete();
    echo "Deleted {$deletedUsers} users from users table\n";
    
    // Clear all clients
    $deletedClients = Capsule::table('clients')->delete();
    echo "Deleted {$deletedClients} clients from clients table\n";
    
    echo "All accounts have been cleared successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
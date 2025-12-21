<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Update or create admin with correct email
$admin = User::updateOrCreate(
    ['email' => 'lila@frizzboss.ca'],
    [
        'name' => 'Lila (FrizzBoss Admin)',
        'password' => Hash::make('password'),
        'is_admin' => true,
        'email_verified_at' => now(),
    ]
);

echo "✅ Admin user ready!\n";
echo "Email: lila@frizzboss.ca\n";
echo "Password: password\n";

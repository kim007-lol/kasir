<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('username', 'kasir')->first();
if ($user) {
    if ($user->role !== 'kasir') {
        echo "Old role: " . $user->role . "\n";
        $user->role = 'kasir';
        $user->save();
        echo "Success: Changed role for username 'kasir' to 'kasir'.\n";
    } else {
        echo "Role is already kasir.\n";
    }
} else {
    echo "User not found.\n";
}

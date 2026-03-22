<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        $now = Carbon::now();

        $users = [
            [
                'email' => 'devidiana@gmail.com',
                'name' => 'Devi Diana Safitri, SPd.',
                'username' => 'admin',
                'password' => 'adminsmegabiz',
                'role' => 'admin',
            ],
            [
                'email' => 'kasir123@gmail.com',
                'name' => 'Kasir SMEGABIZ',
                'username' => 'kasir',
                'password' => 'kasir123',
                'role' => 'kasir',
            ],
            [
                'email' => 'admin@tokoku.com',
                'name' => 'Administrator',
                'username' => 'admin_tokoku',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'email' => 'kasir@tokoku.com',
                'name' => 'Staf Kasir',
                'username' => 'kasir_tokoku',
                'password' => 'password',
                'role' => 'kasir',
            ],
            [
                'email' => 'user@example.com',
                'name' => 'Pelanggan Setia',
                'username' => 'user_example',
                'password' => 'password',
                'role' => 'pelanggan',
            ],
        ];

        foreach ($users as $userData) {
            // SEC: 'role' is NOT in $fillable (removed to prevent mass-assignment privilege escalation)
            // So we must set it explicitly after create/update
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => $now,
                ]
            );
            $user->role = $userData['role'];
            $user->save();
        }
    }

    public function down(): void
    {
        $emails = [
            'devidiana@gmail.com',
            'kasir123@gmail.com',
            'admin@tokoku.com',
            'kasir@tokoku.com',
            'user@example.com',
        ];

        User::whereIn('email', $emails)->delete();
    }
};

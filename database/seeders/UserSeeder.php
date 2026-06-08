<?php

namespace Database\Seeders;

use App\Domain\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            'admin' => [
                'name' => 'Admin Sistem',
                'email' => 'admin@example.com',
                'password' => 'password',
                'status' => 'active',
            ],
            'dosen' => [
                'name' => 'Dosen Mata Kuliah',
                'email' => 'dosen@example.com',
                'password' => 'password',
                'status' => 'active',
            ],
            'mahasiswa' => [
                'name' => 'Mahasiswa Aktif',
                'email' => 'mahasiswa@example.com',
                'password' => 'password',
                'status' => 'active',
            ],
        ];

        foreach ($accounts as $role => $account) {
            User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'role' => $role,
                    'name' => $account['name'],
                    'password' => Hash::make($account['password']),
                    'status' => $account['status'],
                ]
            );
        }
    }
}

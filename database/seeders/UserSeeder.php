<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ganti kata sandi ini segera setelah pertama kali masuk.
        User::updateOrCreate(['nik' => 'MGR001'], [
            'name' => 'Manajemen Awal',
            'password' => 'ganti-sandi-123',
            'role' => Role::Manajemen,
            'status' => AccountStatus::Aktif,
            'approved_at' => now(),
        ]);

        User::updateOrCreate(['nik' => 'TEK001'], [
            'name' => 'Teknisi Awal',
            'password' => 'ganti-sandi-123',
            'role' => Role::Teknisi,
            'status' => AccountStatus::Aktif,
            'approved_at' => now(),
        ]);
    }
}

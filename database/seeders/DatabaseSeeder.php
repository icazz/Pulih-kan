<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Admin Utama',
            'email' => 'admin@pulih.kan',
            'role' => 'admin',
            'phone' => '081234567890',
            'password' => bcrypt('adminpulihkan'),
        ]);

        // Buat 1 Akun User Biasa untuk Tes
        \App\Models\User::factory()->create([
            'name' => 'Ica Zika',
            'email' => 'icha.zika.1@gmail.com',
            'role' => 'user',
            'phone' => '08987654321',
            'password' => bcrypt('obladioblada'),
        ]);
    }
}

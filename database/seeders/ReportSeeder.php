<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\Hash;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan User 'Ica Zika' Ada
        $user = User::firstOrCreate(
            ['email' => 'icha.zika.1@gmail.com'],
            [
                'name' => 'Ica Zika',
                'role' => 'user',
                'phone' => '08987654321',
                'password' => Hash::make('obladioblada'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Buat Laporan Status: MENUNGGU PEMBAYARAN (Kuning)
        Report::create([
            'user_id' => $user->id,
            'title' => 'Pembersihan lumpur didalam rumah',
            'location' => 'Jalan sunan drajat no 19, Lamongan, Jawa Timur',
            'latitude' => '-7.152666',
            'longitude' => '112.409096',
            'description' => 'Terdapat lumpur setinggi 3cm didalam rumah akibat banjir kemarin.',
            'status' => 'pending', // Badge Kuning
            'image_before' => null,
        ]);

        // 3. Buat Laporan Status: DALAM PENGERJAAN (Biru)
        Report::create([
            'user_id' => $user->id,
            'title' => 'Perbaikan Atap dan Plafon',
            'location' => 'Perumahan Graha Indah Blok B, Lamongan',
            'latitude' => '-7.120000',
            'longitude' => '112.415000',
            'description' => 'Atap bocor parah menyebabkan plafon jebol di ruang tengah.',
            'status' => 'process', // Badge Biru
            'image_before' => null,
        ]);

        // 4. Buat Laporan Status: SELESAI (Hijau)
        Report::create([
            'user_id' => $user->id,
            'title' => 'Pemulihan Saluran Air Mampet',
            'location' => 'Desa Turi, Lamongan',
            'latitude' => '-7.110000',
            'longitude' => '112.400000',
            'description' => 'Saluran air depan rumah tersumbat sampah dan lumpur padat.',
            'status' => 'completed', // Badge Hijau
            'image_before' => null,
        ]);

        Report::create([
            'user_id' => $user->id,
            'title' => 'Pemulihan Saluran Air Mampet',
            'location' => 'Desa Turi, Lamongan',
            'latitude' => '-7.110000',
            'longitude' => '112.400000',
            'description' => 'Saluran air depan rumah tersumbat sampah dan lumpur padat.',
            'status' => 'verification', // Badge Hijau
            'image_before' => null,
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        Package::create([
            'name' => 'Monthly Plan',
            'slug' => 'monthly-plan',
            'price' => 150000,
            'duration_in_days' => 30,
            'description' => 'Akses penuh selama 30 hari.',
            'features' => ['Sinyal Trading Harian', 'Analisis Market', 'Grup Diskusi Premium', 'Support Prioritas'],
            'is_active' => true,
            'is_lifetime' => false,
        ]);

        Package::create([
            'name' => 'Yearly Plan',
            'slug' => 'yearly-plan',
            'price' => 1500000,
            'duration_in_days' => 365,
            'description' => 'Hemat 2 bulan dengan langganan tahunan.',
            'features' => ['Semua Fitur Monthly', 'Webinar Eksklusif', 'E-book Trading Mastery', 'Sesi Konsultasi 1-on-1'],
            'is_active' => true,
            'is_lifetime' => false,
        ]);

        Package::create([
            'name' => 'Lifetime Access',
            'slug' => 'lifetime-access',
            'price' => 3500000,
            'duration_in_days' => null,
            'description' => 'Bayar sekali, akses selamanya.',
            'features' => ['Akses Seumur Hidup', 'Semua Fitur Yearly', 'Indikator Trading Premium', 'Akses Fitur Beta'],
            'is_active' => true,
            'is_lifetime' => true,
        ]);
    }
}

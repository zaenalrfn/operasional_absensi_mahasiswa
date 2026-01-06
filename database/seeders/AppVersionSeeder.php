<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppVersion;
use Carbon\Carbon;

class AppVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Bersihkan data lama jika perlu (opsional)
        AppVersion::truncate();

        $versions = [
            [
                'version_number' => '1.3.0',
                'release_date' => Carbon::parse('2026-01-06'),
                'release_notes' => [
                    'Fitur Baru: Ganti Kata Sandi di menu Profil',
                    'Fitur Baru: Halaman Riwayat Pembaruan Aplikasi',
                    'Peningkatan UI: Dukungan Dark Mode pada Halaman Login & Reset Password',
                    'Peningkatan UX: Avatar di Beranda kini dapat diklik menuju Profil',
                    'Perbaikan bug kecil dan peningkatan performa'
                ],
                'is_mandatory' => false,
                'platform' => 'android',
                'download_url' => 'https://play.google.com/store/apps/details?id=com.passion.mahasiswa', // Ganti dengan URL asli
            ],
            [
                'version_number' => '1.2.0',
                'release_date' => Carbon::parse('2026-01-01'),
                'release_notes' => [
                    'Fitur Utama: Dukungan Tema Gelap (Dark Mode) Menyeluruh',
                    'Pola Warna Baru: Penyesuaian warna kartu dan teks agar lebih nyaman di mata',
                    'Perbaikan tampilan pada Halaman Scan & Kamera',
                    'Optimasi Halaman Riwayat Absensi'
                ],
                'is_mandatory' => true, // Update wajib karena perubahan struktur UI besar
                'platform' => 'android',
                'download_url' => 'https://play.google.com/store/apps/details?id=com.passion.mahasiswa',
            ],
            [
                'version_number' => '1.1.0',
                'release_date' => Carbon::parse('2025-12-25'),
                'release_notes' => [
                    'Fitur Baru: Validasi Lokasi saat Absensi',
                    'Fitur Baru: Pengenalan Wajah (Face Recognition) untuk Presensi',
                    'Integrasi Jadwal Kuliah Real-time',
                    'Penambahan logika "Next Course" di Beranda'
                ],
                'is_mandatory' => true,
                'platform' => 'android',
                'download_url' => 'https://play.google.com/store/apps/details?id=com.passion.mahasiswa',
            ],
            [
                'version_number' => '1.0.0',
                'release_date' => Carbon::parse('2025-12-10'),
                'release_notes' => [
                    'Rilis Perdana Aplikasi Passion Mahasiswa',
                    'Fitur Dasar: Login & Logout',
                    'Dashboard Beranda dengan informasi mahasiswa',
                    'Melihat daftar mata kuliah hari ini',
                    'Menu Profil Dasar'
                ],
                'is_mandatory' => false,
                'platform' => 'android',
                'download_url' => 'https://play.google.com/store/apps/details?id=com.passion.mahasiswa',
            ],
        ];

        foreach ($versions as $version) {
            AppVersion::create($version);
        }
    }
}

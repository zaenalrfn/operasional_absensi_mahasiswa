<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lectures;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $lectures = Lectures::all();
        $dosenCount = $lectures->count();

        $coursesData = [
            // Teknik Informatika Semester 5
            ['kode_mk' => '203514-23', 'nama_mk' => 'Mobile & Web Service (RPL)', 'sks' => 3, 'kelas' => 'B', 'hari' => 'Senin', 'jam_mulai' => '12:50:00', 'jam_selesai' => '15:20:00', 'semester' => 5, 'jurusan' => 'Teknik Informatika'],
            ['kode_mk' => '203501-23', 'nama_mk' => 'Agama Islam', 'sks' => 2, 'kelas' => 'B', 'hari' => 'Rabu', 'jam_mulai' => '07:00:00', 'jam_selesai' => '08:40:00', 'semester' => 5, 'jurusan' => 'Teknik Informatika'],
            ['kode_mk' => '203509-23', 'nama_mk' => 'Desain Front-end', 'sks' => 3, 'kelas' => 'A', 'hari' => 'Kamis', 'jam_mulai' => '12:50:00', 'jam_selesai' => '15:20:00', 'semester' => 5, 'jurusan' => 'Teknik Informatika'],
            ['kode_mk' => '203516-23', 'nama_mk' => 'Pengembangan Aplikasi Mobile (RPL)', 'sks' => 3, 'kelas' => 'A', 'hari' => 'Kamis', 'jam_mulai' => '15:30:00', 'jam_selesai' => '18:00:00', 'semester' => 5, 'jurusan' => 'Teknik Informatika'],
            ['kode_mk' => '203507-23', 'nama_mk' => 'Bisnis Digital', 'sks' => 2, 'kelas' => 'B', 'hari' => 'Kamis', 'jam_mulai' => '10:40:00', 'jam_selesai' => '12:20:00', 'semester' => 5, 'jurusan' => 'Sistem Informasi'],
            ['kode_mk' => '203515-23', 'nama_mk' => 'Mobile & We Service Praktik', 'sks' => 2, 'kelas' => 'II', 'hari' => "Jum'at", 'jam_mulai' => '07:00:00', 'jam_selesai' => '10:30:00', 'semester' => 5, 'jurusan' => 'Teknik Informatika'],
            ['kode_mk' => '203508-23', 'nama_mk' => 'Metodologi Penelitian', 'sks' => 2, 'kelas' => 'B', 'hari' => "Jum'at", 'jam_mulai' => '12:50:00', 'jam_selesai' => '14:30:00', 'semester' => 5, 'jurusan' => 'Teknik Informatika'],
            ['kode_mk' => '203510-23', 'nama_mk' => 'Pengantar Big Data', 'sks' => 3, 'kelas' => 'B', 'hari' => 'Selasa', 'jam_mulai' => '09:40:00', 'jam_selesai' => '12:10:00', 'semester' => 5, 'jurusan' => 'Sistem Informasi'],
            // Sains Data Semester 5
            ['kode_mk' => '218501-23', 'nama_mk' => 'Data Science Capstone Project', 'sks' => 5, 'kelas' => 'A', 'hari' => 'Kamis', 'jam_mulai' => '15:30:00', 'jam_selesai' => '17:10:00', 'semester' => 5, 'jurusan' => 'Sains Data'],
            ['kode_mk' => '218502-23F', 'nama_mk' => 'Krenovasi & Kewirausahaan', 'sks' => 2, 'kelas' => 'A', 'hari' => 'Senin', 'jam_mulai' => '10:40:00', 'jam_selesai' => '12:20:00', 'semester' => 5, 'jurusan' => 'Sains Data'],
            ['kode_mk' => '218503-23', 'nama_mk' => 'Keamanan & Privasi Data', 'sks' => 3, 'kelas' => 'A', 'hari' => 'Selasa', 'jam_mulai' => '12:50:00', 'jam_selesai' => '15:20:00', 'semester' => 5, 'jurusan' => 'Sains Data'],
            ['kode_mk' => '218505-23', 'nama_mk' => 'Teknik Pengembangan Model', 'sks' => 3, 'kelas' => 'A', 'hari' => 'Rabu', 'jam_mulai' => '15:30:00', 'jam_selesai' => '18:00:00', 'semester' => 5, 'jurusan' => 'Sains Data'],
            ['kode_mk' => '218507-23', 'nama_mk' => 'Deep Learning', 'sks' => 3, 'kelas' => 'A', 'hari' => 'Kamis', 'jam_mulai' => '15:30:00', 'jam_selesai' => '18:00:00', 'semester' => 5, 'jurusan' => 'Sains Data'],
            ['kode_mk' => '218504-23', 'nama_mk' => 'Etika Profesi Sains Data', 'sks' => 3, 'kelas' => 'A', 'hari' => 'Senin', 'jam_mulai' => '07:50:00', 'jam_selesai' => '10:30:00', 'semester' => 5, 'jurusan' => 'Sains Data'],
            ['kode_mk' => '218506-23', 'nama_mk' => 'Process Mining', 'sks' => 3, 'kelas' => 'A', 'hari' => 'Kamis', 'jam_mulai' => '12:50:00', 'jam_selesai' => '15:20:00', 'semester' => 5, 'jurusan' => 'Sains Data'],
        ];

        foreach ($coursesData as $index => $course) {
            // Ambil dosen secara siklik dari tabel lectures
            $course['dosen_id'] = $lectures[$index % $dosenCount]->id;

            // Set id integer
            $course['id'] = $index + 1;

            Course::create($course);
        }

        $this->command->info('Courses berhasil di-seed lengkap dengan dosen_id dari Lectures.');
    }
}

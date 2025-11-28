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
            ['kode_mk' => '203514-23', 'nama_mk' => 'Mobile & Web Service (RPL)', 'sks' => 3, 'kelas' => 'B', 'hari' => 'Senin', 'jam_mulai' => '12:50:00', 'jam_selesai' => '15:20:00', 'semester' => 5],
            ['kode_mk' => '203501-23', 'nama_mk' => 'Agama Islam', 'sks' => 2, 'kelas' => 'B', 'hari' => 'Rabu', 'jam_mulai' => '07:00:00', 'jam_selesai' => '08:40:00', 'semester' => 5],
            ['kode_mk' => '203509-23', 'nama_mk' => 'Desain Front-end', 'sks' => 3, 'kelas' => 'A', 'hari' => 'Kamis', 'jam_mulai' => '12:50:00', 'jam_selesai' => '15:20:00', 'semester' => 5],
            ['kode_mk' => '203516-23', 'nama_mk' => 'Pengembangan Aplikasi Mobile (RPL)', 'sks' => 3, 'kelas' => 'A', 'hari' => 'Kamis', 'jam_mulai' => '15:30:00', 'jam_selesai' => '18:00:00', 'semester' => 5],
            ['kode_mk' => '203507-23', 'nama_mk' => 'Bisnis Digital', 'sks' => 2, 'kelas' => 'B', 'hari' => 'Kamis', 'jam_mulai' => '10:40:00', 'jam_selesai' => '12:20:00', 'semester' => 5],
        ];

        foreach ($coursesData as $index => $course) {
            $course['dosen_id'] = $lectures[$index % $dosenCount]->id;
            Course::create($course);
        }
    }
}

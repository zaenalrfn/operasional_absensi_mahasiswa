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
            ['kode_mk' => '203514-23', 'nama_mk' => 'Mobile & Web Service (RPL)', 'sks' => 3, 'hari' => 'Senin', 'jam_mulai' => '12:50:00', 'jam_selesai' => '15:20:00', 'semester' => 5, 'jurusan' => 'Teknik Informatika'],
            ['kode_mk' => '203501-23', 'nama_mk' => 'Agama Islam', 'sks' => 2, 'hari' => 'Rabu', 'jam_mulai' => '07:00:00', 'jam_selesai' => '08:40:00', 'semester' => 5, 'jurusan' => 'Teknik Informatika'],
            ['kode_mk' => '203509-23', 'nama_mk' => 'Desain Front-end', 'sks' => 3, 'hari' => 'Kamis', 'jam_mulai' => '12:50:00', 'jam_selesai' => '15:20:00', 'semester' => 5, 'jurusan' => 'Teknik Informatika'],
            ['kode_mk' => '203516-23', 'nama_mk' => 'Pengembangan Aplikasi Mobile (RPL)', 'sks' => 3, 'hari' => 'Kamis', 'jam_mulai' => '15:30:00', 'jam_selesai' => '18:00:00', 'semester' => 5, 'jurusan' => 'Teknik Informatika'],
            ['kode_mk' => '203507-23', 'nama_mk' => 'Bisnis Digital', 'sks' => 2, 'hari' => 'Kamis', 'jam_mulai' => '10:40:00', 'jam_selesai' => '12:20:00', 'semester' => 5, 'jurusan' => 'Teknik Informatika'],
            ['kode_mk' => '203515-23', 'nama_mk' => 'Mobile & We Service Praktik', 'sks' => 2, 'hari' => "Jum'at", 'jam_mulai' => '07:00:00', 'jam_selesai' => '10:30:00', 'semester' => 5, 'jurusan' => 'Teknik Informatika'],
            ['kode_mk' => '203508-23', 'nama_mk' => 'Metodologi Penelitian', 'sks' => 2, 'hari' => "Jum'at", 'jam_mulai' => '12:50:00', 'jam_selesai' => '14:30:00', 'semester' => 5, 'jurusan' => 'Teknik Informatika'],
            ['kode_mk' => '203510-23', 'nama_mk' => 'Pengantar Big Data', 'sks' => 3, 'hari' => 'Selasa', 'jam_mulai' => '09:40:00', 'jam_selesai' => '12:10:00', 'semester' => 5, 'jurusan' => 'Teknik Informatika'],
            // Sains Data Semester 5
            ['kode_mk' => '218501-23', 'nama_mk' => 'Data Science Capstone Project', 'sks' => 5, 'hari' => 'Kamis', 'jam_mulai' => '15:30:00', 'jam_selesai' => '17:10:00', 'semester' => 5, 'jurusan' => 'Sains Data'],
            ['kode_mk' => '218502-23', 'nama_mk' => 'Krenovasi & Kewirausahaan', 'sks' => 2, 'hari' => 'Senin', 'jam_mulai' => '10:40:00', 'jam_selesai' => '12:20:00', 'semester' => 5, 'jurusan' => 'Sains Data'],
            ['kode_mk' => '218503-23', 'nama_mk' => 'Keamanan & Privasi Data', 'sks' => 3, 'hari' => 'Selasa', 'jam_mulai' => '12:50:00', 'jam_selesai' => '15:20:00', 'semester' => 5, 'jurusan' => 'Sains Data'],
            ['kode_mk' => '218505-23', 'nama_mk' => 'Teknik Pengembangan Model', 'sks' => 3, 'hari' => 'Rabu', 'jam_mulai' => '15:30:00', 'jam_selesai' => '18:00:00', 'semester' => 5, 'jurusan' => 'Sains Data'],
            ['kode_mk' => '218507-23', 'nama_mk' => 'Deep Learning', 'sks' => 3, 'hari' => 'Kamis', 'jam_mulai' => '15:30:00', 'jam_selesai' => '18:00:00', 'semester' => 5, 'jurusan' => 'Sains Data'],
            ['kode_mk' => '218504-23', 'nama_mk' => 'Etika Profesi Sains Data', 'sks' => 3, 'hari' => 'Senin', 'jam_mulai' => '07:50:00', 'jam_selesai' => '10:30:00', 'semester' => 5, 'jurusan' => 'Sains Data'],
            ['kode_mk' => '218506-23', 'nama_mk' => 'Process Mining', 'sks' => 3, 'hari' => 'Kamis', 'jam_mulai' => '12:50:00', 'jam_selesai' => '15:20:00', 'semester' => 5, 'jurusan' => 'Sains Data'],
        ];

        $classes = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        // Reset ID autoincrement sequence if possible or just rely on autoincrement.
        // For seeding on fresh db, usually explicit IDs helps but for massive generation, autoincrement is safer.

        foreach ($coursesData as $baseIndex => $courseBase) {
            foreach ($classes as $classIndex => $className) {
                // Create a copy of base course
                $course = $courseBase;

                // Set class specifically
                $course['kelas'] = $className;

                // Append Class to Kode MK to make it unique (e.g., 203514-23-A)
                $course['kode_mk'] = $course['kode_mk'] . '-' . $className;

                // Assign a random lecturer
                $course['dosen_id'] = $lectures->random()->id;

                // Remove explicit ID setting to let DB handle it, or manage it carefully
                // $course['id'] = ... // Better to let autoincrement handle it

                Course::create($course);
            }
        }

        $this->command->info('Courses berhasil di-seed: Setiap MK memiliki kelas A-G.');
    }
}

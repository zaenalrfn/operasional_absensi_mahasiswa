<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentCourse;
use App\Models\User;
use App\Models\Course;

class StudentCoursesSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua mahasiswa
        $students = User::role('mahasiswa')->get();

        // Ambil semua courses
        $courses = Course::all();

        if ($students->isEmpty() || $courses->isEmpty()) {
            $this->command->info('Tidak ada mahasiswa atau course untuk di-seed.');
            return;
        }

        foreach ($students as $student) {
            foreach ($courses as $course) {
                // Check if course matches student's jurusan and semester
                if ($course->jurusan === $student->program_studi && $course->semester == $student->semester) {
                    StudentCourse::create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'hari' => $course->hari,
                        'jam_mulai' => $course->jam_mulai,
                        'jam_selesai' => $course->jam_selesai,
                        'ruangan' => 'Lab ' . rand(1, 5),
                    ]);
                }
            }
        }

        $this->command->info('Semua mahasiswa berhasil diberikan semua course!');
    }
}

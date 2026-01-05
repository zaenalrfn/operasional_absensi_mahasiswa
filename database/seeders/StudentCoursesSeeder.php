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
            // Get courses matching student's jurusan and semester
            $availableCourses = $courses->filter(function ($course) use ($student) {
                return $course->jurusan === $student->program_studi && $course->semester == $student->semester;
            });

            // Group by 'nama_mk' to ensure we pick only ONE class per subject
            $groupedCourses = $availableCourses->groupBy('nama_mk');

            foreach ($groupedCourses as $subjectName => $subjectClasses) {
                // Pick one random class from the available classes (A-G)
                $selectedCourse = $subjectClasses->random();

                StudentCourse::create([
                    'user_id' => $student->id,
                    'course_id' => $selectedCourse->id,
                    'hari' => $selectedCourse->hari,
                    'jam_mulai' => $selectedCourse->jam_mulai,
                    'jam_selesai' => $selectedCourse->jam_selesai,
                    'ruangan' => 'Lab ' . rand(1, 5),
                ]);
            }
        }

        $this->command->info('Semua mahasiswa berhasil diberikan semua course!');
    }
}

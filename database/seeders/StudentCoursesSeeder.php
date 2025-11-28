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
        $user = User::first(); // ambil user pertama
        $courses = Course::all();

        $studentCoursesData = [
            ['course_index' => 2, 'hari' => 'Kamis', 'jam_mulai' => '12:50:00', 'jam_selesai' => '15:20:00', 'ruangan' => 'K1 - D.0.2'],
            ['course_index' => 0, 'hari' => 'Senin', 'jam_mulai' => '12:50:00', 'jam_selesai' => '15:20:00', 'ruangan' => 'K1 - E.0.1'],
            ['course_index' => 1, 'hari' => 'Rabu', 'jam_mulai' => '07:00:00', 'jam_selesai' => '08:40:00', 'ruangan' => 'K1 - D.0.2'],
        ];

        foreach ($studentCoursesData as $sc) {
            StudentCourse::create([
                'user_id' => $user->id,
                'course_id' => $courses[$sc['course_index']]->id,
                'hari' => $sc['hari'],
                'jam_mulai' => $sc['jam_mulai'],
                'jam_selesai' => $sc['jam_selesai'],
                'ruangan' => $sc['ruangan'],
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lectures;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('mahasiswa')) {
            return Inertia::render('Dashboard', [
                'studentStats' => [
                    'coursesTaken' => $user->studentCourses()->count(),
                ],
            ]);
        }

        return Inertia::render('Dashboard', [
            'adminStats' => [
                'totalStudents' => User::role('mahasiswa')->count(),
                'totalCourses' => Course::count(),
                'totalLectures' => User::role('dosen')->count(), // Assuming lecturers are users with role 'dosen'
            ],
        ]);
    }
}

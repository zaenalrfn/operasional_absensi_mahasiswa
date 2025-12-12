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
        return Inertia::render('Dashboard', [
            'totalStudents' => User::count(), // Assuming all users are students for now, or filter by role if needed
            'totalCourses' => Course::count(),
            'totalLectures' => Lectures::count(),
        ]);
    }
}

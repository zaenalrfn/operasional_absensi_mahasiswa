<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        // Include lecturer
        $courses = Course::with('lecturer')->get();
        return response()->json($courses);
    }
}

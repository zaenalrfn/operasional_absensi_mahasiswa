<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentCourse;

class StudentCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentCourse::with(['course.lecturer', 'user']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $data = $query->get();

        return response()->json(['success' => true, 'data' => $data]);
    }
}

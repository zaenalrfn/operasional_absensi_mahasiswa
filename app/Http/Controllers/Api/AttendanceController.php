<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendence;

class AttendanceController extends Controller
{
    /**
     * GET /api/attendances?user_id={id}
     * Mengembalikan list attendances, termasuk relasi course (dan lecturer jika perlu)
     */
    public function index(Request $request)
    {
        $userId = $request->query('user_id');

        $query = Attendence::query()->with(['course.lecturer']);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = $query->orderBy('tanggal', 'desc')->get();

        return response()->json($attendances);
    }

    /**
     * POST /api/attendances
     * Menyimpan data absensi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'method' => 'required|in:manual,face_recognition',
            'photo_capture' => 'nullable|string',
            'verified' => 'boolean',
        ]);

        $attendance = Attendence::create($validated);

        return response()->json($attendance, 201);
    }
}

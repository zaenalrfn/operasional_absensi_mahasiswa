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
}

<?php

namespace App\Http\Controllers;

use App\Models\Attendence;
use App\Models\Course;
use App\Models\StudentCourse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class LectureAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('lecturer')->latest();

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('angkatan')) {
            // Extract last 2 digits of the year (e.g., "2023" -> "23")
            $angkatanSuffix = substr($request->angkatan, -2);
            // Filter where kode_mk ends with "-23"
            $query->where('kode_mk', 'LIKE', "%-{$angkatanSuffix}");
        }

        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        $courses = $query->get();

        // Get unique jurusans and classes for dropdowns
        $jurusans = Course::select('jurusan')->distinct()->whereNotNull('jurusan')->orderBy('jurusan')->pluck('jurusan');
        $classes = Course::select('kelas')->distinct()->whereNotNull('kelas')->orderBy('kelas')->pluck('kelas');

        return Inertia::render('Lecture/Attendance/Index', [
            'courses' => $courses,
            'jurusans' => $jurusans,
            'classes' => $classes,
            'filters' => $request->only(['semester', 'angkatan', 'jurusan', 'kelas']),
        ]);
    }

    public function show($id)
    {
        $course = Course::with('lecturer')->findOrFail($id);

        // Get enrolled students
        $students = StudentCourse::where('course_id', $id)
            ->with('user')
            ->get()
            ->map(function ($sc) {
                return $sc->user;
            })
            ->sortBy('name')
            ->values();

        // Get all attendance records for this course
        $attendances = Attendence::where('course_id', $id)
            ->orderBy('tanggal')
            ->get()
            ->groupBy('tanggal');

        return Inertia::render('Lecture/Attendance/Show', [
            'course' => $course,
            'students' => $students,
            'attendances' => $attendances
        ]);
    }

    public function store(Request $request, $courseId)
    {
        $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.user_id' => 'required|exists:users,id',
            'attendances.*.status' => 'required|in:hadir,izin,sakit,alpha',
        ]);

        DB::transaction(function () use ($request, $courseId) {
            foreach ($request->attendances as $data) {
                // Find existing records for this student, course, and date
                $existingRecords = Attendence::where('course_id', $courseId)
                    ->where('user_id', $data['user_id'])
                    ->whereDate('tanggal', $request->date)
                    ->get();

                if ($existingRecords->count() > 0) {
                    // Update ALL existing records properly
                    foreach ($existingRecords as $attendance) {
                        $attendance->status = $data['status'];
                        $attendance->verified = true;
                        $attendance->save();
                    }
                } else {
                    // Create new record only if none exists
                    Attendence::create([
                        'course_id' => $courseId,
                        'user_id' => $data['user_id'],
                        'tanggal' => $request->date,
                        'status' => $data['status'],
                        'method' => 'manual',
                        'verified' => true,
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Data absensi berhasil disimpan.');
    }
}

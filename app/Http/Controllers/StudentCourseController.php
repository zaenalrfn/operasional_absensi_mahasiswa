<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\StudentCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StudentCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('lecturer')
            ->orderBy('semester')
            ->orderBy('nama_mk');

        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        if ($request->filled('angkatan')) {
            $angkatanSuffix = substr($request->angkatan, -2);
            $query->where('kode_mk', 'LIKE', "%-{$angkatanSuffix}");
        }

        $courses = $query->get()
            ->map(function ($course) {
                $course->is_registered = StudentCourse::where('user_id', Auth::id())
                    ->where('course_id', $course->id)
                    ->exists();
                return $course;
            })
            ->groupBy('semester');

        // Get unique jurusans for the filter dropdown
        $jurusans = Course::select('jurusan')
            ->distinct()
            ->whereNotNull('jurusan')
            ->where('jurusan', '!=', '')
            ->orderBy('jurusan')
            ->pluck('jurusan');

        return Inertia::render('Student/CourseRegistration', [
            'courses' => $courses,
            'jurusans' => $jurusans,
            'filters' => $request->only(['jurusan', 'angkatan']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($request->course_id);

        // Check if already registered
        $exists = StudentCourse::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Anda sudah terdaftar di mata kuliah ini.');
        }

        StudentCourse::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'hari' => $course->hari,
            'jam_mulai' => $course->jam_mulai,
            'jam_selesai' => $course->jam_selesai,
            'ruangan' => 'Online', // Or fetch from somewhere if available
        ]);

        return redirect()->back()->with('success', 'Berhasil mendaftar mata kuliah.');
    }

    public function destroy($id)
    {
        // Optional: Allow dropping a course
        $studentCourse = StudentCourse::where('user_id', Auth::id())
            ->where('course_id', $id)
            ->firstOrFail();

        $studentCourse->delete();

        return redirect()->back()->with('success', 'Mata kuliah berhasil dibatalkan.');
    }
}

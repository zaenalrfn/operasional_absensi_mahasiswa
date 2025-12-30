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

        $user = Auth::user();

        // Filter by user's Program Studi
        if ($user->program_studi) {
            $query->where('jurusan', $user->program_studi);
        }

        if ($request->filled('angkatan')) {
            $angkatanSuffix = substr($request->angkatan, -2);
            $query->where('kode_mk', 'LIKE', "%-{$angkatanSuffix}");
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
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
        $jurusans = [];
        if ($user->program_studi) {
            $jurusans = [$user->program_studi];
        } else {
            $jurusans = Course::select('jurusan')
                ->distinct()
                ->whereNotNull('jurusan')
                ->where('jurusan', '!=', '')
                ->orderBy('jurusan')
                ->pluck('jurusan');
        }

        return Inertia::render('Student/CourseRegistration', [
            'courses' => $courses,
            'jurusans' => $jurusans,
            'userMajor' => $user->program_studi,
            'filters' => $request->only(['jurusan', 'angkatan', 'semester']),
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

    public function myCourses()
    {
        $courses = StudentCourse::with(['course.lecturer']) // Eager load course and lecturer
            ->where('user_id', Auth::id())
            ->get()
            ->map(function ($studentCourse) {
                $course = $studentCourse->course;
                $course->ruangan = $studentCourse->ruangan; // Inject ruangan from StudentCourse
                return $course;
            });

        return Inertia::render('Student/MyCourses', [
            'courses' => $courses,
        ]);
    }
}

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
        $user = auth()->user();

        // If user is a lecturer, find their legacy Lecture ID using email
        if ($user->hasRole('dosen')) {
            $lecture = \App\Models\Lectures::where('email', $user->email)->first();
            if ($lecture) {
                $query->where('dosen_id', $lecture->id);
            } else {
                // If no legacy record found, show empty (or handle as needed)
                $query->where('id', -1);
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_mk', 'LIKE', "%{$request->search}%")
                    ->orWhere('kode_mk', 'LIKE', "%{$request->search}%");
            });
        }

        $courses = $query->get();

        return Inertia::render('Lecture/Attendance/Index', [
            'courses' => $courses,
            'filters' => $request->only(['search']),
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
        // Get all unique dates for this course to map to "Meetings"
        $groupByDate = Attendence::where('course_id', $id)
            ->orderBy('tanggal')
            ->get()
            ->groupBy(function ($attendance) {
                return \Carbon\Carbon::parse($attendance->tanggal)->format('Y-m-d');
            });

        // Create a map of Date -> Meeting Number (1-based index)
        // keys of $groupByDate are 'YYYY-MM-DD' sorted asc
        $meetingDates = $groupByDate->keys()->values(); // List of dates ['2023-10-01', '2023-10-08', ...]

        $attendances = $groupByDate; // Keep the group structure for easy lookup by date

        return Inertia::render('Lecture/Attendance/Show', [
            'course' => $course,
            'students' => $students,
            'attendances' => $attendances,
            'meetingDates' => $meetingDates, // Pass this to help frontend map 'YYYY-MM-DD' <-> 1..14
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

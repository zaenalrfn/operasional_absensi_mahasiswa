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

        // Check if user is a Dosen (Lecturer)
        if ($user->hasRole('dosen')) {
            // Find lecturer record by email
            $lecture = Lectures::where('email', $user->email)->first();

            if ($lecture) {
                // Get courses taught by this lecturer
                $courses = Course::where('dosen_id', $lecture->id)->get();
                $courseIds = $courses->pluck('id');

                // Total students across all courses
                $totalStudents = \App\Models\StudentCourse::whereIn('course_id', $courseIds)
                    ->distinct('user_id')
                    ->count('user_id');

                // Total attendance records
                $totalAttendances = \App\Models\Attendence::whereIn('course_id', $courseIds)->count();

                // Attendance breakdown
                $attendanceStats = [
                    'hadir' => \App\Models\Attendence::whereIn('course_id', $courseIds)->where('status', 'hadir')->count(),
                    'sakit' => \App\Models\Attendence::whereIn('course_id', $courseIds)->where('status', 'sakit')->count(),
                    'izin' => \App\Models\Attendence::whereIn('course_id', $courseIds)->where('status', 'izin')->count(),
                    'alpha' => \App\Models\Attendence::whereIn('course_id', $courseIds)->where('status', 'alpha')->count(),
                ];

                // Per-course statistics
                $courseStats = $courses->map(function ($course) {
                    $totalStudents = \App\Models\StudentCourse::where('course_id', $course->id)->count();
                    $totalAttendances = \App\Models\Attendence::where('course_id', $course->id)->count();
                    $presentCount = \App\Models\Attendence::where('course_id', $course->id)
                        ->where('status', 'hadir')
                        ->count();

                    $attendanceRate = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100) : 0;

                    return [
                        'name' => $course->nama_mk,
                        'kelas' => $course->kelas,
                        'students' => $totalStudents,
                        'attendanceRate' => $attendanceRate,
                        'totalMeetings' => \App\Models\Attendence::where('course_id', $course->id)
                            ->select(\DB::raw('DATE(tanggal) as date'))
                            ->distinct()
                            ->count(),
                    ];
                });

                // Recent activity (last 7 days)
                $recentActivity = \App\Models\Attendence::whereIn('course_id', $courseIds)
                    ->where('tanggal', '>=', now()->subDays(7))
                    ->selectRaw('DATE(tanggal) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'date' => \Carbon\Carbon::parse($item->date)->format('d M'),
                            'count' => $item->count,
                        ];
                    });

                return Inertia::render('Dashboard', [
                    'lecturerStats' => [
                        'totalCourses' => $courses->count(),
                        'totalStudents' => $totalStudents,
                        'totalAttendances' => $totalAttendances,
                        'attendanceOverview' => $attendanceStats,
                        'coursePerformance' => $courseStats,
                        'recentActivity' => $recentActivity,
                        'averageAttendanceRate' => $totalAttendances > 0
                            ? round(($attendanceStats['hadir'] / $totalAttendances) * 100)
                            : 0,
                    ],
                ]);
            }
        }

        // Student Dashboard
        if ($user->hasRole('mahasiswa')) {
            // Calculate Attendance Stats
            $attendances = \App\Models\Attendence::where('user_id', $user->id)->get();

            $stats = [
                'hadir' => $attendances->where('status', 'hadir')->count(),
                'sakit' => $attendances->where('status', 'sakit')->count(),
                'izin' => $attendances->where('status', 'izin')->count(),
                'alpha' => $attendances->where('status', 'alpha')->count(),
            ];

            // Per Course Attendance Rate
            $courseStats = $user->studentCourses->map(function ($studentCourse) use ($user) {
                $course = $studentCourse->course;
                if (!$course)
                    return null;

                $total = \App\Models\Attendence::where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->count();

                $present = \App\Models\Attendence::where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->where('status', 'hadir')
                    ->count();

                return [
                    'name' => $course->nama_mk,
                    'rate' => $total > 0 ? round(($present / $total) * 100) : 0,
                    'total_meetings' => $total
                ];
            })->filter()->values();

            return Inertia::render('Dashboard', [
                'studentStats' => [
                    'coursesTaken' => $user->studentCourses()->count(),
                    'attendanceOverview' => $stats,
                    'coursePerformance' => $courseStats
                ],
            ]);
        }

        // Admin/Super-Admin Dashboard
        $totalStudents = User::role('mahasiswa')->count();
        $totalCourses = Course::count();
        $totalLecturers = User::role('dosen')->count();
        $totalAttendances = \App\Models\Attendence::count();

        // Attendance by Status
        $attendanceByStatus = [
            'hadir' => \App\Models\Attendence::where('status', 'hadir')->count(),
            'sakit' => \App\Models\Attendence::where('status', 'sakit')->count(),
            'izin' => \App\Models\Attendence::where('status', 'izin')->count(),
            'alpha' => \App\Models\Attendence::where('status', 'alpha')->count(),
        ];

        // Students by Department
        $studentsByDepartment = User::role('mahasiswa')
            ->select('program_studi', \DB::raw('count(*) as total'))
            ->groupBy('program_studi')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->program_studi,
                    'total' => $item->total
                ];
            });

        // Top 5 Courses by Enrollment
        $topCourses = Course::withCount('studentCourses')
            ->orderBy('student_courses_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($course) {
                return [
                    'name' => $course->nama_mk,
                    'students' => $course->student_courses_count ?? 0,
                ];
            });

        // Attendance Trend (Last 7 days)
        $attendanceTrend = \App\Models\Attendence::where('tanggal', '>=', now()->subDays(7))
            ->selectRaw('DATE(tanggal) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => \Carbon\Carbon::parse($item->date)->format('d M'),
                    'count' => $item->count,
                ];
            });

        // Recent Activities (Last 10 attendance records)
        $recentActivities = \App\Models\Attendence::with(['user', 'course'])
            ->latest('created_at')
            ->take(10)
            ->get()
            ->map(function ($attendance) {
                return [
                    'student' => $attendance->user->name ?? 'Unknown',
                    'course' => $attendance->course->nama_mk ?? 'Unknown',
                    'status' => $attendance->status,
                    'date' => $attendance->tanggal,
                    'time' => $attendance->created_at->format('H:i'),
                ];
            });

        // Overall Attendance Rate
        $overallAttendanceRate = $totalAttendances > 0
            ? round(($attendanceByStatus['hadir'] / $totalAttendances) * 100)
            : 0;

        return Inertia::render('Dashboard', [
            'adminStats' => [
                'totalStudents' => $totalStudents,
                'totalCourses' => $totalCourses,
                'totalLecturers' => $totalLecturers,
                'totalAttendances' => $totalAttendances,
                'overallAttendanceRate' => $overallAttendanceRate,
                'attendanceByStatus' => $attendanceByStatus,
                'studentsByDepartment' => $studentsByDepartment,
                'topCourses' => $topCourses,
                'attendanceTrend' => $attendanceTrend,
                'recentActivities' => $recentActivities,
            ],
        ]);
    }
}

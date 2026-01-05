<?php

namespace App\Http\Controllers;

use App\Models\Attendence;
use App\Models\StudentCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StudentAttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get courses taken by the student
        $studentCourses = StudentCourse::with(['course.lecturer'])
            ->where('user_id', $user->id)
            ->get();

        $coursesData = $studentCourses->map(function ($sc) use ($user) {
            $course = $sc->course;

            // Get attendance records for this user in this course
            $attendances = Attendence::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->orderBy('tanggal')
                ->get();

            // Map attendances to "Pertemuan ke-X"
            // We assume the meeting number is based on the chronological order of ALL attendance dates for this course
            // OR just the records for this student. 
            // Better approach: Get ALL unique dates for the course to determine "Meeting 1" date, "Meeting 2" date, etc.
            // Then check if the student has a record for that date.

            $allCourseDates = Attendence::where('course_id', $course->id)
                ->select('tanggal')
                ->distinct()
                ->orderBy('tanggal')
                ->pluck('tanggal')
                ->toArray();

            // Prepare 14 meetings data
            $meetings = array_fill(1, 14, null); // 1 to 14

            foreach ($allCourseDates as $index => $date) {
                // Meeting number is index + 1
                $meetingNumber = $index + 1;
                if ($meetingNumber > 14)
                    break; // Limit to 14 per design

                // Find student's record for this date
                $record = $attendances->first(function ($att) use ($date) {
                    return $att->tanggal->format('Y-m-d') === \Carbon\Carbon::parse($date)->format('Y-m-d');
                });

                if ($record) {
                    $meetings[$meetingNumber] = strtoupper(substr($record->status, 0, 1)); // 'h' -> 'H', 'a' -> 'A'
                } else {
                    // If date exists but no record for student:
                    // It implies they might be absent (Alpha) OR raw data missing.
                    // For now, let's leave valid date empty as '?' or handled by frontend?
                    // User requirement: "mahasiswa yang telat absen... di edit".
                    // The system seems to rely on explicit records. If no record, maybe not yet graded?
                    // Let's pass null if no record.
                }
            }

            return [
                'id' => $course->id,
                'nama_mk' => $course->nama_mk,
                'kode_mk' => $course->kode_mk,
                'sks' => $course->sks,
                'jadwal' => "{$course->hari}, Jam {$course->jam_mulai}-{$course->jam_selesai}",
                'meetings' => $meetings,
            ];
        });

        return Inertia::render('Student/Attendance/Index', [
            'courses' => $coursesData
        ]);
    }
}

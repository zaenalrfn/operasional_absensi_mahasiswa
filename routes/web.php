<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('/daftar-mahasiswa', [App\Http\Controllers\PublicStudentController::class, 'create'])->name('student.register');
Route::post('/daftar-mahasiswa', [App\Http\Controllers\PublicStudentController::class, 'store'])->name('student.register.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/lecture/attendance', [App\Http\Controllers\LectureAttendanceController::class, 'index'])->name('lecture.attendance.index');
    Route::get('/lecture/attendance/{course}', [App\Http\Controllers\LectureAttendanceController::class, 'show'])->name('lecture.attendance.show');
    Route::post('/lecture/attendance/{course}', [App\Http\Controllers\LectureAttendanceController::class, 'store'])->name('lecture.attendance.store');

    Route::resource('lectures', App\Http\Controllers\LectureController::class);
    Route::resource('courses', App\Http\Controllers\CourseController::class);
    Route::resource('students', App\Http\Controllers\StudentController::class);
    Route::resource('attendance-locations', App\Http\Controllers\AttendanceLocationController::class);

    Route::get('/student/course-registration', [App\Http\Controllers\StudentCourseController::class, 'index'])->name('student.course-registration');
    Route::post('/student/course-registration', [App\Http\Controllers\StudentCourseController::class, 'store'])->name('student.course-registration.store');
    Route::delete('/student/course-registration/{course}', [App\Http\Controllers\StudentCourseController::class, 'destroy'])->name('student.course-registration.destroy');
    Route::get('/student/my-courses', [App\Http\Controllers\StudentCourseController::class, 'myCourses'])->name('student.my-courses');
});

require __DIR__ . '/settings.php';

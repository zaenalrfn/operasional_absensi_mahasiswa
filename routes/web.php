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

    Route::get('/lecture/attendance', [App\Http\Controllers\LectureAttendanceController::class, 'index'])->name('lecture.attendance.index')->middleware(['permission:attendance.view_any']);
    Route::get('/lecture/attendance/{course}', [App\Http\Controllers\LectureAttendanceController::class, 'show'])->name('lecture.attendance.show')->middleware(['permission:attendance.view_any']);
    Route::post('/lecture/attendance/{course}', [App\Http\Controllers\LectureAttendanceController::class, 'store'])->name('lecture.attendance.store')->middleware(['permission:attendance.update']);

    // Lectures
    Route::resource('lectures', App\Http\Controllers\LectureController::class)->except(['index', 'show'])->middleware(['role:admin|super-admin']);
    Route::resource('lectures', App\Http\Controllers\LectureController::class)->only(['index', 'show'])->middleware(['permission:lecture.view_any']);

    // Courses - Only Admin and Super-Admin can access
    Route::resource('courses', App\Http\Controllers\CourseController::class)->middleware(['role:admin|super-admin']);

    // Students - Only Admin and Super-Admin can access
    Route::resource('students', App\Http\Controllers\StudentController::class)->middleware(['role:admin|super-admin']);

    Route::resource('attendance-locations', App\Http\Controllers\AttendanceLocationController::class)->middleware(['role:admin|super-admin']);
    Route::resource('app-versions', App\Http\Controllers\AppVersionController::class)->middleware(['role:admin|super-admin']);

    Route::get('/student/course-registration', [App\Http\Controllers\StudentCourseController::class, 'index'])->name('student.course-registration')->middleware(['permission:student_course.view_own']);
    Route::post('/student/course-registration', [App\Http\Controllers\StudentCourseController::class, 'store'])->name('student.course-registration.store')->middleware(['permission:student_course.create']);
    Route::delete('/student/course-registration/{course}', [App\Http\Controllers\StudentCourseController::class, 'destroy'])->name('student.course-registration.destroy')->middleware(['permission:student_course.delete']);
    Route::get('/student/attendance', [App\Http\Controllers\StudentAttendanceController::class, 'index'])->name('student.attendance.index')->middleware(['permission:attendance.view_own']);
    Route::get('/student/my-courses', [App\Http\Controllers\StudentCourseController::class, 'myCourses'])->name('student.my-courses')->middleware(['permission:student_course.view_own']);
});

require __DIR__ . '/settings.php';

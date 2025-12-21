<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\StudentCourseController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\AttendanceController;

// Current User Route
Route::get('/current-user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/reset-password', [\App\Http\Controllers\Api\ResetPasswordController::class, 'reset']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Student Courses Routes
Route::middleware('auth:sanctum')->get('/student-courses', [StudentCourseController::class, 'index']);

// Courses and Attendances Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/attendances', [AttendanceController::class, 'index']);
    Route::get('/attendances', [AttendanceController::class, 'index']);
    Route::post('/attendances', [AttendanceController::class, 'store']);
    Route::get('/attendance-locations', [\App\Http\Controllers\AttendanceLocationController::class, 'apiIndex']);
});

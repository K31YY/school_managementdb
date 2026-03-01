<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import Controllers for API Routes
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\ClassSectionController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\ScheduleDetailController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\StudyController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReportLogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Routes (No Authentication Required)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
// Auth Actions
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user-profile', [AuthController::class, 'profile']);

// Protected Routes (Require Authentication)
// if you want to protect all routes, you can wrap them in a middleware group like this:
Route::middleware('auth:sanctum')->group(function () {



    // use apiResource for CRUD operations on each entity, which automatically creates routes for index, store, show, update, and destroy actions

    // User Management
    Route::apiResource('users', UserController::class);
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('students', StudentController::class);

    // School Management
    Route::apiResource('academic-years', AcademicYearController::class);
    Route::apiResource('academic-years', AcademicYearController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('class-sections', ClassSectionController::class);

    // Scheduling
    Route::apiResource('schedules', ScheduleController::class);
    Route::apiResource('schedule-details', ScheduleDetailController::class);

    // Attendance and Studies
    Route::apiResource('attendances', AttendanceController::class);
    Route::apiResource('studies', StudyController::class);
    Route::apiResource('leave-requests', LeaveRequestController::class);

    // Notifications and Report Logs
    Route::apiResource('notifications', NotificationController::class);
    Route::apiResource('report-logs', ReportLogController::class);
});

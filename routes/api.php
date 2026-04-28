<?php

use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\Api\DashboardController;



/*

|--------------------------------------------------------------------------

| API Routes

|--------------------------------------------------------------------------

*/



// Public Routes (not requiring Token/Login)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Auth Actions
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user-profile', [AuthController::class, 'profile']);

// Protected Routes (requiring Token/Login) - if Laravel Passport please use middleware('auth:api')
// if using Laravel Sanctum, use middleware('auth:sanctum')
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/absent-students-today', [AttendanceController::class, 'getAbsentToday']);
    Route::get('/attendance-report', [AttendanceController::class, 'attendanceReport']);
    Route::get('/scores-by-class', [StudyController::class, 'getScoresByClass']);
    Route::get('/students/report', [App\Http\Controllers\Api\StudentController::class, 'reportStudentList']);
    // for get class sections list
    Route::get('/sections/list', [ClassSectionController::class, 'getSectionList']);
    // for get student attendance records
    Route::get('/my-attendance', [AttendanceController::class, 'myAttendance']);
    // for teacher to get attendance records of their students
    Route::post('/attendance/store', [AttendanceController::class, 'store']);

    Route::get('/my-profile', [StudentController::class, 'myProfile']);
    // for get student results 
    Route::get('/my-results', [StudyController::class, 'myResults']);
    Route::get('/studies/check', [StudyController::class, 'checkScore']);
    Route::post('/change-password', [App\Http\Controllers\Api\AuthController::class, 'changePassword']);

    // Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData']);
    Route::get('/dashboard-counts', [DashboardController::class, 'getCounts']);

    // Grouping routes by functionality for better organization
    // Group User, Teacher, Student Management
    Route::apiResource('users', UserController::class);
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('students', StudentController::class);
    // Group Academic Management

    Route::apiResource('academic-years', AcademicYearController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('class-sections', ClassSectionController::class);
    // Group Schedule Management

    Route::apiResource('schedules', ScheduleController::class);
    Route::apiResource('schedule-details', ScheduleDetailController::class);
    // Group Attendance and Study Management

    Route::apiResource('attendances', AttendanceController::class);
    Route::apiResource('studies', StudyController::class);
    Route::apiResource('leave-requests', LeaveRequestController::class);
    // Group Notification and Report Management

    Route::apiResource('notifications', NotificationController::class);
    Route::apiResource('report-logs', ReportLogController::class);
});

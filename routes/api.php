<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import Controllers ទាំងអស់
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

// 1. ក្រុម Public Routes (មិនទាមទារ Login ទេ - សម្រាប់ Login/Register)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
// Auth Actions
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user-profile', [AuthController::class, 'profile']);

// 2. ក្រុម Protected Routes (ទាមទារការប្រើប្រាស់ Token/Login ទើបអាចប្រើបាន)
// ប្រសិនបើអ្នកប្រើ Laravel Sanctum សូមប្រើ middleware('auth:sanctum')
Route::middleware('auth:sanctum')->group(function () {



    // ប្រើ apiResource វានឹងបង្កើត Route ទាំង ៥ (index, store, show, update, destroy) ឱ្យអូតូ

    // ក្រុមគ្របគ្រង User & Profiles
    Route::apiResource('users', UserController::class);
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('students', StudentController::class);

    // ក្រុមរចនាសម្ព័ន្ធសាលារៀន
    Route::apiResource('academic-years', AcademicYearController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('class-sections', ClassSectionController::class);

    // ក្រុមរៀបចំកាលវិភាគ
    Route::apiResource('schedules', ScheduleController::class);
    Route::apiResource('schedule-details', ScheduleDetailController::class);

    // ក្រុមប្រតិបត្តិការ និងការសិក្សា
    Route::apiResource('attendances', AttendanceController::class);
    Route::apiResource('studies', StudyController::class);
    Route::apiResource('leave-requests', LeaveRequestController::class);

    // ក្រុមប្រព័ន្ធ
    Route::apiResource('notifications', NotificationController::class);
    Route::apiResource('report-logs', ReportLogController::class);
});

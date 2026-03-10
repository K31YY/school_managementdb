<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * get count of teachers and students for dashboard statistics
     */
    public function getCounts(): JsonResponse
    {
        try {
            $teachers = Teacher::where('IsDeleted', 0)->count();
            $students = Student::where('IsDeleted', 0)->count();

            return response()->json([
                'success' => true,
                'teachers' => $teachers,
                'students' => $students
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
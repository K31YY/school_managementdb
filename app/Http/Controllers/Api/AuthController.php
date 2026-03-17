<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
   public function login(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'LoginKey' => 'required',
        'Password' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'message' => 'Please check your credentials'], 400);
    }

    $loginKey = $request->LoginKey;
    $password = $request->Password;

    // 1. Check Admin
    $admin = User::where('Username', $loginKey)->first();
    if ($admin) {
        if (Hash::check($password, $admin->Password)) {
            return $this->respondWithToken($admin, 'admin', $admin->Username);
        }
        return response()->json(['success' => false, 'message' => 'Incorrect admin password'], 401);
    }

    // 2. Check Teacher
    $teacher = Teacher::where('Email', $loginKey)->where('IsDeleted', 0)->first();
    if ($teacher) {
        if (Hash::check($password, $teacher->password)) {
            return $this->respondWithToken($teacher, 'teacher', $teacher->TeacherName);
        }
        return response()->json(['success' => false, 'message' => 'Incorrect teacher password'], 401);
    }

    // 3. Check Student
    $student = Student::where('Email', $loginKey)->where('IsDeleted', 0)->first();
    if ($student) {
        if (Hash::check($password, $student->password)) {
            return $this->respondWithToken($student, 'student', $student->StuName);
        }
        return response()->json(['success' => false, 'message' => 'Incorrect student password'], 401);
    }

    return response()->json(['success' => false, 'message' => 'User not found'], 401);
    }

    /**
     * Function to generate token and respond with user info
     * update ID to be consistent across all roles and add formatted ID for Flutter
     */
    private function respondWithToken($model, $role, $displayName)
    {
        $token = $model->createToken('auth_token')->plainTextToken;

        // Manage user ID based on role
        $userId = null;
        if ($role == 'admin') {
            $userId = $model->UserID;
        } else if ($role == 'teacher') {
            $userId = $model->TeacherID; 
        } else if ($role == 'student') {
            $userId = $model->StudentID;
        }

        // add formatted ID for Flutter (e.g., UTB001, UTB002, etc.)
        $formattedId = "UTB" . str_pad($userId, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'success'      => true,
            'message'      => 'Login successfully',
            'token'        => $token,
            'role'         => $role,
            'display_name' => $displayName,
            'user'         => [
            'id'   => (int)$userId,
            'formatted_id' => $formattedId,
            'info' => $model
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }
}
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
        // validate input
        $validator = Validator::make($request->all(), [
            'LoginKey' => 'required', // user name or email
            'Password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'please check your credentials'], 400);
        }

        $loginKey = $request->LoginKey;
        $password = $request->Password;

        // check in order: Admin -> Teacher -> Student
        $admin = User::where('Username', $loginKey)->first();
        if ($admin && Hash::check($password, $admin->Password)) {
            return $this->respondWithToken($admin, 'admin', $admin->Username);
        }

        // if not admin, check in Teacher table (use Email and 'password' in lowercase)
        $teacher = Teacher::where('Email', $loginKey)->where('IsDeleted', 0)->first();
        if ($teacher && Hash::check($password, $teacher->password)) {
            return $this->respondWithToken($teacher, 'teacher', $teacher->TeacherName);
        }

        // if not teacher, check in Student table (use Email and 'password' in lowercase)
        $student = Student::where('Email', $loginKey)->where('IsDeleted', 0)->first();
        if ($student && Hash::check($password, $student->password)) {
            return $this->respondWithToken($student, 'student', $student->StuName);
        }

        // if no match found, return error
        return response()->json([
            'success' => false,
            'message' => 'invalid credentials'
        ], 401);
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

        // ២. បម្លែងលេខ ID ទៅជាទម្រង់ UTB001, UTB002...
    // str_pad($rawId, 3, '0', STR_PAD_LEFT) មានន័យថា ថែមលេខ 0 ឱ្យគ្រប់ 3 ខ្ទង់
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
            // បោះទៅជា 'id' ជានិច្ចដើម្បីឱ្យ Flutter ស្រួលចាប់យក
            'info' => $model   // ព័ត៌មានលម្អិតផ្សេងៗ
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Logged out successfully'], 200);
    }
}
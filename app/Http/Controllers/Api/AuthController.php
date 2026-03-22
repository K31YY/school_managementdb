<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Change Password Logic
     * Handles Admin (Password) and Teacher/Student (password) dynamically.
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed', 
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Logic: Get authenticated user via Sanctum Token
        $user = $request->user(); 

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        // Logic: Check if table uses 'Password' (Admin) or 'password' (Teacher/Student)
        $attributes = $user->getAttributes();
        $passwordColumn = array_key_exists('Password', $attributes) ? 'Password' : 'password';

        // Verify Current Password
        if (!Hash::check($request->current_password, $user->{$passwordColumn})) {
            return response()->json([
                'success' => false, 
                'message' => 'The current password you entered is incorrect.'
            ], 422);
        }

        // Update the database
        $user->update([
            $passwordColumn => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ], 200);
    }

    /**
     * Login Logic
     */
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
     * Token Generation & User Info Response
     */
    private function respondWithToken($model, $role, $displayName)
    {
        $token = $model->createToken('auth_token')->plainTextToken;

        $userId = null;
        if ($role == 'admin') {
            $userId = $model->UserID;
        } else if ($role == 'teacher') {
            $userId = $model->TeacherID; 
        } else if ($role == 'student') {
            // FIX: Changed from StudentID to StuID to match your Student model
            $userId = $model->StuID; 
        }

        $formattedId = "UTB" . str_pad($userId, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'success'      => true,
            'message'      => 'Login successfully',
            'token'        => $token,
            'role'         => $role,
            'display_name' => $displayName,
            'user'         => [
                'id'           => (int)$userId,
                'formatted_id' => $formattedId,
                'info'         => $model
            ]
        ], 200);
    }

    /**
     * Logout Logic
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }
}
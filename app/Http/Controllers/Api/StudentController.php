<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * 1. Get the information of the student who is currently logged in (for My Profile page in Flutter)
     */
    public function myProfile(Request $request)
    {
        // $request->user() will return the authenticated user based on the token provided in the request header
        $student = $request->user();

        if (!$student) {
            return response()->json([
                'success' => false, 
                'message' => 'Student not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student
        ], 200);
    }

    /**
     * show all students (Index)
     */
    public function index()
    {
        $students = Student::where('IsDeleted', 0)
            ->orderBy('StuID', 'desc')
            ->get();
            
        return response()->json(['success' => true, 'data' => $students], 200);
    }

    /**
     * create new student (Store)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'StuNameEN' => 'required|string|max:255',
            'Email'     => 'required|email|unique:tblstudents,Email',
            'password'  => 'required|min:6',
            'Photo'     => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->all();

            // hash Password
            $data['password'] = Hash::make($request->password);

            // manage file upload for Photo, if no photo uploaded, set default photo path
            if ($request->hasFile('Photo')) {
                $data['Photo'] = $request->file('Photo')->store('students', 'public');
            } else {
                $data['Photo'] = 'students/default.png';
            }

            $data['IsDeleted'] = 0;
            $data['Status'] = $request->Status ?? 1;

            $student = Student::create($data);

            return response()->json([
                'success' => true, 
                'message' => 'Store successful', 
                'data' => $student
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * update (Update)
     */
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'Email' => 'nullable|email|unique:tblstudents,Email,' . $id . ',StuID',
            'Photo' => 'nullable|image|max:2048',
            'password' => 'nullable|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        // if has new password, hash it
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        // if has new photo, delete old one and store new one
        if ($request->hasFile('Photo')) {
            if ($student->Photo && $student->Photo !== 'students/default.png') {
                Storage::disk('public')->delete($student->Photo);
            }
            $data['Photo'] = $request->file('Photo')->store('students', 'public');
        }

        $student->update($data);

        return response()->json([
            'success' => true, 
            'message' => 'Update successful', 
            'data' => $student
        ], 200);
    }

    /**
     * 5. Delete data (Soft Delete)
     */
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'Delete successful'], 200);
    }
}

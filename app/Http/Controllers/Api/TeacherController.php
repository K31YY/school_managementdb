<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    /**
     * Show all teachers (excluding soft-deleted ones)
     */
    public function index()
    {
        $teachers = Teacher::where('IsDeleted', 0)
            ->orderBy('TeacherID', 'desc')
            ->get();
            
        return response()->json(['success' => true, 'data' => $teachers], 200);
    }

    /**
     * Create a new teacher record
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UserID'      => 'required',
            'TeacherName' => 'required|string',
            'Email'       => 'required|email|unique:tblteachers,Email',
            'password'    => 'required|min:6',
            'Photo'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->all();
            
            // Logic: Hash the password before saving
            $data['password'] = Hash::make($request->password);
            $data['IsDeleted'] = 0;

            // Handle Photo Upload
            if ($request->hasFile('Photo')) {
                $data['Photo'] = $request->file('Photo')->store('teachers', 'public');
            } else {
                $data['Photo'] = 'teachers/default.png'; 
            }

            $teacher = Teacher::create($data);

            return response()->json(['success' => true, 'message' => 'Teacher saved successfully', 'data' => $teacher], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update teacher record
     */
    public function update(Request $request, $id)
    {
        // Logic: Use TeacherID as the lookup key
        $teacher = Teacher::where('TeacherID', $id)->first();
        
        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'Email'    => 'nullable|email|unique:tblteachers,Email,' . $id . ',TeacherID',
            'Photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->except(['Photo', 'password']);

        // Logic: Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Manage File Upload for Teacher Photo
        if ($request->hasFile('Photo')) {
            // Delete old photo if it's not the default
            if ($teacher->Photo && $teacher->Photo !== 'teachers/default.png') {
                Storage::disk('public')->delete($teacher->Photo);
            }
            $data['Photo'] = $request->file('Photo')->store('teachers', 'public');
        }

        // Rigorous Update: Use fill() and save() to ensure all attributes are updated
        $teacher->fill($data);
        $teacher->save();

        return response()->json(['success' => true, 'message' => 'Updated successfully', 'data' => $teacher], 200);
    }

    /**
     * Soft delete teacher
     */
    public function destroy($id)
    {
        $teacher = Teacher::where('TeacherID', $id)->first();
        
        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher not found'], 404);
        }

        $teacher->update(['IsDeleted' => 1]);
        
        return response()->json(['success' => true, 'message' => 'Deleted successfully'], 200);
    }
}
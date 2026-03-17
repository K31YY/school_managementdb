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
     * show all teachers
     */
    public function index()
    {
        $teachers = Teacher::where('IsDeleted', 0)->orderBy('TeacherID', 'desc')->get();
        return response()->json(['success' => true, 'data' => $teachers], 200);
    }

    /**
     * create a new teacher record
     */
    public function store(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'UserID'      => 'required',
        'TeacherName' => 'required|string',
        'Email'       => 'required|email|unique:tblteachers,Email',
        'password'    => 'required|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    try {
        $teacher = new Teacher();
        $teacher->UserID      = $request->UserID; 
        $teacher->TeacherName = $request->TeacherName;
        $teacher->Gender      = $request->Gender;
        $teacher->DOB         = $request->DOB;
        $teacher->Phone       = $request->Phone;
        $teacher->Email       = $request->Email;
        $teacher->password    = Hash::make($request->password); 
        $teacher->Address     = $request->Address;
        $teacher->StartDate   = $request->StartDate;
        $teacher->EndDate     = $request->EndDate;
        $teacher->Specialty   = $request->Specialty;
        $teacher->Certificate = $request->Certificate;
        $teacher->IsDeleted   = 0;

        // FIXED: Check for file, if not found, set default.png
        if ($request->hasFile('Photo')) {
            $path = $request->file('Photo')->store('teachers', 'public');
            $teacher->Photo = $path;
        } else {
            $teacher->Photo = 'teachers/default.png'; 
        }

        $teacher->save();

        return response()->json(['success' => true, 'message' => 'saved successfully'], 201);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    /**
     * update teacher record
     */
    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'Email' => 'nullable|email|unique:tblteachers,Email,' . $id . ',TeacherID',
            'Photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Manage File Upload for Teacher Photo (if provided), otherwise keep existing photo
        if ($request->filled('UserID')) {
            $teacher->UserID = $request->UserID;
        }

        if ($request->filled('password')) {
            $teacher->password = Hash::make($request->password);
        }

        if ($request->hasFile('Photo')) {
            if ($teacher->Photo && $teacher->Photo !== 'teachers/default.png') {
                Storage::disk('public')->delete($teacher->Photo);
            }
            $teacher->Photo = $request->file('Photo')->store('teachers', 'public');
        }

        // Update ព័ត៌មានផ្សេងៗ
        $teacher->update($request->except(['Photo', 'password', 'UserID']));
        $teacher->save();

        return response()->json(['success' => true, 'message' => 'updated successfully', 'data' => $teacher], 200);
    }


    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'deleted successfully'], 200);
    }
}

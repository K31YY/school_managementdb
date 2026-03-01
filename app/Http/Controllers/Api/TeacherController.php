<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public function index()
    {
        // Get All Teachers with User Relation, Exclude Deleted Records, Ordered by TeacherID Descending
        $teachers = Teacher::with('user')
            ->where('IsDeleted', 0)
            ->orderBy('TeacherID', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $teachers]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TeacherName' => 'required|string|max:255',
            'UserID'      => 'nullable|exists:tblusers,UserID',
            'Email'       => 'nullable|email|unique:tblteachers,Email',
            'Photo'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Image Upload Handling with Default Value if No File is Uploaded
        $path = null;
        if ($request->hasFile('Photo')) {
            $path = $request->file('Photo')->store('teachers', 'public');
        } else {
            $path = 'teachers/default.png';
        }

        // create new teacher record with mass assignment, make sure to allow the fields in the Teacher model's $fillable property
        $teacher = Teacher::create([
            'UserID'      => $request->UserID,
            'TeacherName' => $request->TeacherName,
            'Gender'      => $request->Gender,
            'DOB'         => $request->DOB,
            'Phone'       => $request->Phone,
            'Email'       => $request->Email,
            'Specialty'   => $request->Specialty,
            'Address'     => $request->Address,
            'StartDate'   => $request->StartDate,
            'EndDate'     => $request->EndDate,
            'Certificate' => $request->Certificate,
            'Photo'       => $path,
            'IsDeleted'   => 0,
        ]);

        return response()->json(['success' => true, 'data' => $teacher], 201);
    }

    public function show($id)
    {
        // Find Teacher by ID with User Relation, Check if Exists and Not Deleted Before Returning
        $teacher = Teacher::with('user')->find($id);
        
        if (!$teacher || $teacher->IsDeleted == 1) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $teacher]);
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::find($id);
        if (!$teacher) return response()->json(['message' => 'Teacher not found'], 404);

        $data = $request->except('Photo');

        if ($request->hasFile('Photo')) {
            // Delete Old Photo if Exists and Not Default, Then Store New Photo
            if ($teacher->Photo && $teacher->Photo !== 'teachers/default.png') {
                Storage::disk('public')->delete($teacher->Photo);
            }
            $data['Photo'] = $request->file('Photo')->store('teachers', 'public');
        }

        $teacher->update($data);

        return response()->json(['success' => true, 'data' => $teacher]);
    }

    public function destroy($id)
    {
        $teacher = Teacher::find($id);
        if (!$teacher) return response()->json(['message' => 'Teacher not found'], 404);

        // Use Soft Delete by Setting IsDeleted to 1 Instead of Deleting the Record from the Database
        $teacher->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'Deleted teacher data successfully']);
    }
}

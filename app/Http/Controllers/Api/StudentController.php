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
     * show all students
     */
    public function index()
    {
        $students = Student::where('IsDeleted', 0)->orderBy('StuID', 'desc')->get();
        return response()->json(['success' => true, 'data' => $students], 200);
    }

    /**
     * Create a new student record
     */
    public function store(Request $request)
    {
        // Check Required Fields and Unique Email, and Validate Photo Upload
        $validator = Validator::make($request->all(), [
            'StuName'  => 'required|string|max:255',
            'Email'    => 'required|email|unique:tblstudents,Email',
            'password' => 'required|min:6',
            'Gender'   => 'required',
            'DOB'      => 'required|date',
            'Phone'    => 'required',
            'Photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Manage File Upload for Student Photo (if provided), otherwise use default photo
        $path = $request->hasFile('Photo')
            ? $request->file('Photo')->store('students', 'public')
            : 'students/default.png';

        // Insert New Student Record with Hashed Password and Default Values for Status and IsDeleted
        $student = Student::create([
            'UserID'        => $request->UserID,        
            'StuName'       => $request->StuName,       
            'StuNameKH'     => $request->StuNameKH,     
            'StuNameEN'     => $request->StuNameEN,     
            'Gender'        => $request->Gender,        
            'DOB'           => $request->DOB,           
            'POB'           => $request->POB,           
            'Address'       => $request->Address,       
            'Phone'         => $request->Phone,         
            'Email'         => $request->Email,         
            'password'      => Hash::make($request->password), 
            'Promotion'     => $request->Promotion,     
            'Photo'         => $path,                   
            'FatherName'    => $request->FatherName,    
            'FatherJob'     => $request->FatherJob,     
            'MotherName'    => $request->MotherName,    
            'MotherJob'     => $request->MotherJob,     
            'FamilyContact' => $request->FamilyContact, 
            'Status'        => $request->Status ?? 1,   // Default 1
            'IsDeleted'     => 0,                       // Default 0
        ]);

        return response()->json(['success' => true, 'message' => 'saved successfully', 'data' => $student], 201);
    }

    /**
     * show information of a student by ID (only if not deleted)
     */
    public function show($id)
    {
        $student = Student::find($id);
        if (!$student || $student->IsDeleted == 1) {
            return response()->json(['success' => false, 'message' => 'student not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $student], 200);
    }

    /**
     * Edit information of a student by ID (including handling password and photo updates)
     */
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'Email' => 'nullable|email|unique:tblstudents,Email,' . $id . ',StuID',
            'Photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Edit password if provided
        if ($request->filled('password')) {
            $student->password = Hash::make($request->password);
        }


        // Edit Photo if provided
        if ($request->hasFile('Photo')) {
            if ($student->Photo && $student->Photo !== 'students/default.png') {
                Storage::disk('public')->delete($student->Photo);
            }
            $student->Photo = $request->file('Photo')->store('students', 'public');
        }

        // Edit all other fields except Photo and password (which we handled separately)
        $student->update($request->except(['Photo', 'password']));

        return response()->json(['success' => true, 'message' => 'updated successfully', 'data' => $student], 200);
    }

    /**
     * Delete (Soft Delete by Setting IsDeleted to 1)
     */
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        // change IsDeleted to 1 instead of actually deleting the record
        $student->update(['IsDeleted' => 1]); 
        return response()->json(['success' => true, 'message' => 'deleted successfully'], 200);
    }
}

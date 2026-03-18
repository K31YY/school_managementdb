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
    public function index()
    {
        $students = Student::where('IsDeleted', 0)->orderBy('StuID', 'desc')->get();
        return response()->json(['success' => true, 'data' => $students], 200);
    }

    public function store(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'UserID'    => 'required',
            'StuNameEN' => 'required',
            'Email'     => 'required|email|unique:tblstudents,Email',
            'password'  => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $student = new Student();
            
            // 2. Assignment (Ensure names match migration exactly)
            $student->UserID        = $request->UserID;
            $student->StuName       = $request->StuName;
            $student->StuNameKH     = $request->StuNameKH;
            $student->StuNameEN     = $request->StuNameEN;
            $student->Gender        = $request->Gender;
            $student->DOB           = $request->DOB;
            $student->POB           = $request->POB;
            $student->Address       = $request->Address;
            $student->Phone         = $request->Phone;
            $student->Email         = $request->Email;
            
            // NOTE: If your Student Model has 'password' => 'hashed' in casts, 
            // do NOT use Hash::make here. Just save the plain text.
            $student->password      = $request->password; 
            
            $student->Promotion     = $request->Promotion;
            $student->FatherName    = $request->FatherName;
            $student->FatherJob     = $request->FatherJob;
            $student->MotherName    = $request->MotherName;
            $student->MotherJob     = $request->MotherJob;
            $student->FamilyContact = $request->FamilyContact;
            $student->Status        = $request->Status ?? 1;
            $student->IsDeleted     = 0;

            if ($request->hasFile('Photo')) {
                $student->Photo = $request->file('Photo')->store('students', 'public');
            } else {
                $student->Photo = 'students/default.png';
            }

            $student->save();
            return response()->json(['success' => true, 'message' => 'Saved successfully'], 201);

        } catch (\Exception $e) {
            // This returns the exact error (like missing column or foreign key error)
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

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

        if ($request->filled('password')) {
            $student->password = Hash::make($request->password);
        }

        if ($request->hasFile('Photo')) {
            if ($student->Photo && $student->Photo !== 'students/default.png') {
                Storage::disk('public')->delete($student->Photo);
            }
            $student->Photo = $request->file('Photo')->store('students', 'public');
        }

        $student->update($request->except(['Photo', 'password']));
        return response()->json(['success' => true, 'message' => 'Updated successfully'], 200);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->update(['IsDeleted' => 1]); 
        return response()->json(['success' => true, 'message' => 'Deleted successfully'], 200);
    }
}
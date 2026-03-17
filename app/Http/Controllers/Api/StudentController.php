<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
    // Log the full request so you can see it in storage/logs/laravel.log
    Log::info("Request Data: ", $request->all());

    $validator = Validator::make($request->all(), [
        'StuNameEN' => 'required',
        'Email'     => 'required|email',
        'password'  => 'required',
    ]);

    if ($validator->fails()) {
        Log::error("Validation Failed: ", $validator->errors()->toArray());
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $path = $request->hasFile('Photo')
            ? $request->file('Photo')->store('students', 'public')
            : 'students/default.png';

    Student::create([
        'UserID'        => $request->UserID, // Make sure DB column is VARCHAR
        'StuName'       => $request->StuNameEN, 
        'StuNameKH'     => $request->StuNameKH,
        'StuNameEN'     => $request->StuNameEN,
        'Gender'        => $request->Gender,
        'DOB'           => $request->DOB, // Ensure this is 'YYYY-MM-DD'
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
        'Status'        => 1,
        'IsDeleted'     => 0,
    ]);

    return response()->json(['message' => 'Success'], 201);
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

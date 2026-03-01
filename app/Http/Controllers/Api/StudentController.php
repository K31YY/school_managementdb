<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Exception;

class StudentController extends Controller
{
    // Show All Students with User Info, Ordered by StuID Descending and IsDeleted = 0
    public function index()
    {
        $students = Student::with('user')
            ->where('IsDeleted', 0)
            ->orderBy('StuID', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $students]);
    }

    // Insert New Student with Validation and Default Values
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'StuNameKH' => 'required|string|max:255',
            'StuNameEN' => 'required|string|max:255',
            'Gender'    => 'required',
            'Email'     => 'nullable|email|unique:tblstudents,Email',
            'Photo'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            // Image Upload Handling with Default Value if No File is Uploaded
            $path = 'students/default.png';
            if ($request->hasFile('Photo')) {
                $path = $request->file('Photo')->store('students', 'public');
            }

            // Insert New Student with Mass Assignment, Make Sure to Allow the Fields in the Student Model's $fillable Property
            $student = Student::create([
                // if UserID is not provided in the request, it will be set to null, which is acceptable if the database allows null values for this field
                'UserID'        => $request->UserID ?? null, 
                'StuName'       => $request->StuName,
                'StuNameKH'     => $request->StuNameKH,
                'StuNameEN'     => $request->StuNameEN,
                'Gender'        => $request->Gender,
                'DOB'           => $request->DOB,
                'POB'           => $request->POB,
                'Address'       => $request->Address,
                'Phone'         => $request->Phone,
                'Email'         => $request->Email,
                'Promotion'     => $request->Promotion,
                'Photo'         => $path,
                'FatherName'    => $request->FatherName,
                'FatherJob'     => $request->FatherJob,
                'MotherName'    => $request->MotherName,
                'MotherJob'     => $request->MotherJob,
                'FamilyContact' => $request->FamilyContact,
                'Status'        => $request->Status ?? 1,
                'IsDeleted'     => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inserted student data successfully',
                'data' => $student
            ], 201);

        } catch (Exception $e) {
            // if there is an error during the student creation process, return a JSON response with the error message and a 500 Internal Server Error status code
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // show specific student by ID with related user, attendances, studies, and requests data
    public function show($id)
    {
        $student = Student::with(['user', 'attendances', 'studies', 'requests'])->find($id);

        if (!$student || $student->IsDeleted == 1) {
            return response()->json(['success' => false, 'message' => 'Student not found or deleted'], 404);
        }

        return response()->json(['success' => true, 'data' => $student]);
    }

    // Update Student Data with Validation and Image Handling
    public function update(Request $request, $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }


        try {
            $data = $request->all();
            if ($request->hasFile('Photo')) {
                if ($student->Photo && $student->Photo !== 'students/default.png') {
                    Storage::disk('public')->delete($student->Photo);
                }
                $data['Photo'] = $request->file('Photo')->store('students', 'public');
            }

            $student->update($data);
            return response()->json(['success' => true, 'data' => $student]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Delete Student by ID using Soft Delete (IsDeleted = 1)
    public function destroy($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        $student->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'Deleted student data successfully']);
    }
}

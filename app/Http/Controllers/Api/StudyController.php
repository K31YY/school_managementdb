<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Study;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Student;
class StudyController extends Controller
{

    // function to get the results of the logged-in student, calculate grades, and group by semester
    public function myResults(Request $request)
{
    try {
        // find the student linked to the logged-in user
        $student = $request->user(); 

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // get the studies with the subject relationship
        $results = \App\Models\Study::with(['subject'])
            ->where('StuID', $student->StuID)
            ->get()
            ->map(function ($item) {
                // if Grade is empty, calculate it based on TotalScore
                if (empty($item->Grade)) {
                    $score = $item->TotalScore; // assuming TotalScore is already calculated and stored in the database

                    if ($score >= 90) $item->Grade = 'A';
                    elseif ($score >= 80) $item->Grade = 'B';
                    elseif ($score >= 70) $item->Grade = 'C';
                    elseif ($score >= 60) $item->Grade = 'D';
                    elseif ($score >= 50) $item->Grade = 'E';
                    else $item->Grade = 'F';
                }
                return $item;
            });

        // manage the results by grouping them by semester
        $grouped = $results->groupBy('Semester');

        return response()->json([
            'success' => true,
            'data' => $grouped
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}






    public function index()
    {
   // Get All Studies with Related Student, Subject, and Academic Year Data, Ordered by CreatedDate Descending
        $studies = Study::with(['student', 'subject', 'academicYear'])->get();
        return response()->json(['success' => true, 'data' => $studies]);
    }

    public function store(Request $request)
    {
        // Update Validation to Use Laravel's Validator and Provide Clear Error Messages
        $validator = Validator::make($request->all(), [
            'StuID'           => 'required|exists:tblstudents,StuID',
            'SubID'           => 'required|exists:tblsubjects,SubID',
            'YearID'          => 'required|exists:tblacademicyears,YearID',
            'Quiz'            => 'nullable|numeric',
            'Homework'        => 'nullable|numeric',
            'AttendanceScore' => 'nullable|numeric',
            'Participation'   => 'nullable|numeric',
            'Midterm'         => 'nullable|numeric',
            'Final'           => 'nullable|numeric',
            'Semester'        => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        // Calculate Total Score as the Sum of Quiz, Homework, AttendanceScore, Participation, Midterm, and Final
        $data = $request->all();
        $data['TotalScore'] =
        ($request->Quiz ?? 0) +
        ($request->Homework ?? 0) +                      
        ($request->AttendanceScore ?? 0) +
        ($request->Participation ?? 0) +                      
        ($request->Midterm ?? 0) +
        ($request->Final ?? 0);
        $study = Study::create($data);
        return response()->json(['success' => true, 'data' => $study], 201);
    }

    public function show($id)
    {
        $study = Study::with(['student', 'subject', 'academicYear'])->find($id);
        if (!$study) {
            return response()->json(['success' => false, 'message' => 'Study not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $study]);
    }

    public function update(Request $request, $id)
    {
        $study = Study::find($id);
        if (!$study) return response()->json(['success' => false, 'message' => 'Study not found'], 404);
        $study->update($request->all());
        return response()->json(['success' => true, 'data' => $study]);
    }

    public function destroy($id)
    {
        $study = Study::find($id);
        if (!$study) return response()->json(['success' => false, 'message' => 'Study not found'], 404);
        $study->delete();
        return response()->json(['success' => true, 'message' => 'Deleted study data successfully']);
    }
    public function getMyResults(Request $request)
    {
    // Find student ID linked to the logged-in user
    $student = \App\Models\Student::where('UserID', $request->user()->id)->first();
    if (!$student) {
        return response()->json(['success' => false, 'message' => 'No student linked to this user'], 404);
    }
    // Get studies with the subject relationship
    $results = \App\Models\Study::with(['subject'])
        ->where('StuID', $student->StuID)
        ->get();
    return response()->json(['success' => true, 'data' => $results], 200);
    }
}
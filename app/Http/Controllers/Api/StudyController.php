<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Study;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudyController extends Controller
{
    public function index()
    {
        // ទាញយកទិន្នន័យសិក្សាជាមួយ Student, Subject និង AcademicYear
        $studies = Study::with(['student', 'subject', 'academicYear'])->get();
        return response()->json(['success' => true, 'data' => $studies]);
    }

    public function store(Request $request)
    {
        // កែ Key ឱ្យត្រូវតាម fillable ក្នុង Model និង Database
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

        // គណនា TotalScore មុននឹងរក្សាទុក (ស្រេចចិត្ត)
        $data = $request->all();
        $data['TotalScore'] = ($request->Quiz ?? 0) + ($request->Homework ?? 0) + 
                             ($request->AttendanceScore ?? 0) + ($request->Participation ?? 0) + 
                             ($request->Midterm ?? 0) + ($request->Final ?? 0);

        $study = Study::create($data);
        return response()->json(['success' => true, 'data' => $study], 201);
    }

    public function show($id)
    {
        $study = Study::with(['student', 'subject', 'academicYear'])->find($id);

        if (!$study) {
            return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យសិក្សានេះទេ'], 404);
        }
        return response()->json(['success' => true, 'data' => $study]);
    }

    public function update(Request $request, $id)
    {
        $study = Study::find($id);
        if (!$study) return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        $study->update($request->all());
        return response()->json(['success' => true, 'data' => $study]);
    }

    public function destroy($id)
    {
        $study = Study::find($id);
        if (!$study) return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        $study->delete();
        return response()->json(['success' => true, 'message' => 'លុបទិន្នន័យជោគជ័យ']);
    }
}

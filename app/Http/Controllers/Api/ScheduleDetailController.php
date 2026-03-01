<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScheduleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleDetailController extends Controller
{
    public function index()
    {
        // បង្ហាញម៉ោងសិក្សាទាំងអស់ជាមួយព័ត៌មានគ្រូ មុខវិជ្ជា និងបន្ទប់
        $details = ScheduleDetail::with(['teacher', 'subject', 'room', 'schedule.classSection'])->get();
        return response()->json(['success' => true, 'data' => $details]);
    }

   public function store(Request $request)
    {
    // ត្រូវប្រើឈ្មោះ Key ឱ្យដូចក្នុង Postman របស់អ្នកបេះបិទ
    $validator = Validator::make($request->all(), [
        'ScheduleID' => 'required|exists:tblschedules,ScheduleID',
        'TeacherID'  => 'required|exists:tblteachers,TeacherID',
        'SubID'      => 'required|exists:tblsubjects,SubID', // ត្រូវប្រើ SubID តាម Diagram
        'RoomID'     => 'required|exists:tblrooms,RoomID',
        'DayOfWeek'  => 'required|string',
        'StartTime'  => 'required',
        'EndTime'    => 'required',
    ]);

    if ($validator->fails()) {
        // បើនៅតែ Error វានឹងប្រាប់ថា Key មួយណាដែលខុស
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    // បង្កើតទិន្នន័យ
    $det = ScheduleDetail::create($request->all());
    
    return response()->json([
        'success' => true, 
        'message' => 'បញ្ចូលម៉ោងសិក្សាជោគជ័យ',
        'data' => $det
    ], 201);
    }


    public function show($id)
    {
        $detail = ScheduleDetail::with(['teacher', 'subject', 'room', 'schedule.classSection'])->find($id);

        if (!$detail) {
            return response()->json(['success' => false, 'message' => 'រកមិនឃើញព័ត៌មានម៉ោងសិក្សានេះទេ'], 404);
        }
        return response()->json(['success' => true, 'data' => $detail]);
    }

    public function update(Request $request, $id)
    {
        $detail = ScheduleDetail::find($id);
        if (!$detail) return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        $detail->update($request->all());
        return response()->json(['success' => true, 'data' => $detail]);
    }

    public function destroy($id)
    {
        $detail = ScheduleDetail::find($id);
        if (!$detail) return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        $detail->delete(); 
        return response()->json(['success' => true, 'message' => 'លុបម៉ោងសិក្សាជោគជ័យ']);
    }
}

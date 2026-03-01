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
        // Show All Schedule Details with Teacher, Subject, Room, and Schedule's Class Section Info, Ordered by DetailID Descending
        $details = ScheduleDetail::with(['teacher', 'subject', 'room', 'schedule.classSection'])->get();
        return response()->json(['success' => true, 'data' => $details]);
    }

   public function store(Request $request)
    {
    // must validate the request data before creating a new schedule detail
    $validator = Validator::make($request->all(), [
        'ScheduleID' => 'required|exists:tblschedules,ScheduleID',
        'TeacherID'  => 'required|exists:tblteachers,TeacherID',
        'SubID'      => 'required|exists:tblsubjects,SubID',
        'RoomID'     => 'required|exists:tblrooms,RoomID',
        'DayOfWeek'  => 'required|string',
        'StartTime'  => 'required',
        'EndTime'    => 'required',
    ]);

    if ($validator->fails()) {
        // if still fails, return the validation errors in the response with a 422 Unprocessable Entity status code
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    // Create the schedule detail using mass assignment, make sure to allow the fields in the ScheduleDetail model's $fillable property
    $det = ScheduleDetail::create($request->all());
    
    return response()->json([
        'success' => true, 
        'message' => 'Inserted schedule detail successfully',
        'data' => $det
    ], 201);
    }


    public function show($id)
    {
        $detail = ScheduleDetail::with(['teacher', 'subject', 'room', 'schedule.classSection'])->find($id);

        if (!$detail) {
            return response()->json(['success' => false, 'message' => 'Schedule detail not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $detail]);
    }

    public function update(Request $request, $id)
    {
        $detail = ScheduleDetail::find($id);
        if (!$detail) return response()->json(['success' => false, 'message' => 'Schedule detail not found'], 404);

        $detail->update($request->all());
        return response()->json(['success' => true, 'data' => $detail]);
    }

    public function destroy($id)
    {
        $detail = ScheduleDetail::find($id);
        if (!$detail) return response()->json(['success' => false, 'message' => 'Schedule detail not found'], 404);

        $detail->delete(); 
        return response()->json(['success' => true, 'message' => 'Deleted schedule detail successfully']);
    }
}

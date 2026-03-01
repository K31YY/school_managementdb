<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with(['student', 'scheduleDetail.subject'])->get();
        return response()->json(['success' => true, 'data' => $attendances]);
    }

    public function store(Request $request)
    {
        // Insert Multiple Records (Batch Insert)
        if ($request->has('attendance_data') && is_array($request->attendance_data)) {
            foreach ($request->attendance_data as $record) {
                Attendance::create([
                    'StuID'    => $record['StuID'],
                    'DetailID' => $record['DetailID'],
                    'AttDate'  => $record['AttDate'],
                    'Status'   => $record['Status'], 
                    'Reason'   => $record['Reason'] ?? null,
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Inserted multiple attendance records successfully'], 201);
        }

        // Validation for Single Record
        $validator = Validator::make($request->all(), [
            'StuID'    => 'required|exists:tblstudents,StuID',
            'DetailID' => 'required|exists:tblscheduledetails,DetailID',
            'AttDate'  => 'required|date',
            'Status'   => 'required|string',
            'Reason'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $att = Attendance::create([
            'StuID'    => $request->StuID,
            'DetailID' => $request->DetailID,
            'AttDate'  => $request->AttDate,
            'Status'   => $request->Status,
            'Reason'   => $request->Reason,
        ]);

        return response()->json(['success' => true, 'data' => $att], 201);
    }

    public function show($id)
    {
        $attendance = Attendance::with(['student', 'scheduleDetail.subject', 'scheduleDetail.teacher'])->find($id);
        if (!$attendance) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        return response()->json(['success' => true, 'data' => $attendance]);
    }

    public function update(Request $request, $id)
    {
    $attendance = Attendance::find($id);

    if (!$attendance) {
        return response()->json(['success' => false, 'message' => 'Not found'], 404);
    }

    // Add validation for the fields that can be updated
    $validator = Validator::make($request->all(), [
        'Status' => 'required|string|max:50',
        'Reason' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    // use fillable fields to update the attendance record
    $attendance->fill($request->only(['Status', 'Reason', 'StuID', 'DetailID', 'AttDate']));
    $attendance->save();

    return response()->json([
        'success' => true,
        'message' => 'Updated attendance record successfully',
        'data' => $attendance
    ]);
    }

    public function destroy($id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        $attendance->delete();
        return response()->json(['success' => true, 'message' => 'Deleted attendance record successfully']);
    }
}

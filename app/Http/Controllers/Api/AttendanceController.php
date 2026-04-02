<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class AttendanceController extends Controller
{
    // show all attendance records with student and subject details
    public function index()
    {
        try {
            // use eager loading to get student and subject details with attendance records
            $attendances = Attendance::with(['student', 'scheduleDetail.subject'])->get();
            return response()->json(['success' => true, 'data' => $attendances]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // insert attendance record, support both single and multiple records
    public function store(Request $request)
    {
        try {
            // insert multiple attendance records if attendance_data array is provided
            if ($request->has('attendance_data') && is_array($request->attendance_data)) {
                $validator = Validator::make($request->all(), [
                    'attendance_data.*.StuID'    => 'required|exists:tblstudents,StuID',
                    'attendance_data.*.DetailID' => 'required|exists:tblscheduledetails,DetailID',
                    'attendance_data.*.AttDate'  => 'required|date',
                    'attendance_data.*.Status'   => 'required|string',
                ]);

                if ($validator->fails()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
                }

                foreach ($request->attendance_data as $record) {
                    Attendance::create([
                        'StuID'    => $record['StuID'],
                        'DetailID' => $record['DetailID'],
                        'AttDate'  => $record['AttDate'],
                        'Status'   => $record['Status'],
                        'Reason'   => $record['Reason'] ?? null,
                    ]);
                }
                return response()->json(['success' => true, 'message' => 'Attendance records created successfully'], 201);
            }

            // validate single attendance record
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

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()], 500);
        }
    }

    // show attendance by id with student and subject details
    public function show($id)
    {
        $attendance = Attendance::with(['student', 'scheduleDetail.subject', 'scheduleDetail.teacher'])->find($id);
        
        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'attendance not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $attendance]);
    }

    // update attendance
    public function update(Request $request, $id)
    {
        try {
            $attendance = Attendance::find($id);

            if (!$attendance) {
                return response()->json(['success' => false, 'message' => 'attendance not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'Status'  => 'sometimes|required|string|max:50',
                'Reason'  => 'nullable|string',
                'AttDate' => 'sometimes|required|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $attendance->update($request->only(['Status', 'Reason', 'StuID', 'DetailID', 'AttDate']));

            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully',
                'data' => $attendance
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // delete attendance record
    public function destroy($id)
    {
        $attendance = Attendance::find($id);
        
        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'attendance not found'], 404);
        }

        $attendance->delete();
        return response()->json(['success' => true, 'message' => 'attendance deleted successfully']);
    }
}

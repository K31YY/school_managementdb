<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class AttendanceController extends Controller
{

    public function getAbsentToday()
    {
        try {
            $today = now()->format('Y-m-d');

            // get all attendance records for today where status is 'A' (Absent) and include student details
            $absents = Attendance::with('student')
                ->where('AttDate', $today)
                ->where('Status', 'A')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $absents->map(function ($item) {
                    return [
                        'StuNameKH' => $item->student->StuName ?? 'N/A',
                        'ClassName' => 'Grade 12',
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function attendanceReport(Request $request)
    {
        try {
            $stuID = $request->stu_id;
            $fromDate = $request->from;
            $toDate = $request->to;

            $results = DB::table('tblattendances')
                ->join('tblscheduledetails', 'tblattendances.DetailID', '=', 'tblscheduledetails.DetailID')
                ->join('tblsubjects', 'tblscheduledetails.SubID', '=', 'tblsubjects.SubID')
                ->select([
                    'tblattendances.AttDate as date',
                    'tblsubjects.SubName as subject',
                    'tblattendances.Status as status'
                ])
                ->where('tblattendances.StuID', $stuID)
                ->whereBetween('tblattendances.AttDate', [$fromDate, $toDate])
                ->orderBy('tblattendances.AttDate', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function myAttendance(Request $request)
    {
        try {
            $student = $request->user();

            $query = DB::table('tblattendances')
                ->join('tblscheduledetails', 'tblattendances.DetailID', '=', 'tblscheduledetails.DetailID')
                ->join('tblsubjects', 'tblscheduledetails.SubID', '=', 'tblsubjects.SubID')
                ->select([
                    'tblsubjects.SubName as SubjectName',
                    DB::raw("SUM(CASE WHEN tblattendances.Status = 'P' THEN 1 ELSE 0 END) as present_count"),
                    DB::raw("SUM(CASE WHEN tblattendances.Status = 'A' THEN 1 ELSE 0 END) as absent_count")
                ])
                ->where('tblattendances.StuID', $student->StuID);

            if ($request->month) {
                $query->whereMonth('tblattendances.AttDate', $request->month);
            }

            if ($request->year) {
                $query->whereYear('tblattendances.AttDate', $request->year);
            }

            $results = $query->groupBy('tblsubjects.SubName')->get();

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
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
    public function show(int $id)
    {
        $attendance = Attendance::with(['student', 'scheduleDetail.subject', 'scheduleDetail.teacher'])->find($id);

        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'attendance not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $attendance]);
    }

    // update attendance
    public function update(Request $request, int $id)
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
    public function destroy(int $id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'attendance not found'], 404);
        }

        $attendance->delete();
        return response()->json(['success' => true, 'message' => 'attendance deleted successfully']);
    }
}

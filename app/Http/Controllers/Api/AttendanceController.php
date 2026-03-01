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
        // ១. ករណីកត់វត្តមានសិស្សច្រើននាក់ (Bulk Insert)
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
            return response()->json(['success' => true, 'message' => 'កត់វត្តមានសិស្សទាំងអស់ជោគជ័យ'], 201);
        }

        // ២. ករណីកត់វត្តមានម្តងម្នាក់ (កែសម្រួល Validation ត្រង់ Status កុំឱ្យឆាប់ Error)
        $validator = Validator::make($request->all(), [
            'StuID'    => 'required|exists:tblstudents,StuID',
            'DetailID' => 'required|exists:tblscheduledetails,DetailID',
            'AttDate'  => 'required|date',
            'Status'   => 'required|string', // ប្ដូរពី "in:..." មក "string" ដើម្បីកុំឱ្យរើសអើងអក្សរតូចធំ
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
        if (!$attendance) return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យ'], 404);
        return response()->json(['success' => true, 'data' => $attendance]);
    }

    public function update(Request $request, $id)
    {
    $attendance = Attendance::find($id);

    if (!$attendance) {
        return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យ'], 404);
    }

    // បន្ថែម Validation ដើម្បីការពារ Error Column Status
    $validator = Validator::make($request->all(), [
        'Status' => 'required|string|max:50', // កំណត់ប្រវែងអក្សរឱ្យត្រូវតាម Database
        'Reason' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    // ប្រើ fill() និង save() ដើម្បីឱ្យកាន់តែច្បាស់លាស់
    $attendance->fill($request->only(['Status', 'Reason', 'StuID', 'DetailID', 'AttDate']));
    $attendance->save();

    return response()->json([
        'success' => true,
        'message' => 'កែប្រែវត្តមានជោគជ័យ',
        'data' => $attendance
    ]);
    }

    public function destroy($id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        $attendance->delete();
        return response()->json(['success' => true, 'message' => 'លុបទិន្នន័យវត្តមានជោគជ័យ']);
    }
}

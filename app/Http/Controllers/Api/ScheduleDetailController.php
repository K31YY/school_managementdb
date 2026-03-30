<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScheduleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleDetailController extends Controller
{
    public function index(Request $request)
    {
        // Get Schedule Details with related info,
        $query = ScheduleDetail::with([
            'subject', 
            'teacher', 
            'room', 
            'schedule.academicYear', 
            'schedule.classSection'
        ])->where('IsDeleted', 0);

        // filter by schedule if provided (e.g., for a specific class section)
        if ($request && $request->has('section_id')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->where('SectionID', $request->section_id);
            });
        }

        $details = $query->orderBy('StartTime', 'asc')->get();

        return response()->json([
            'success' => true, 
            'data' => $details
        ], 200);
    }

    public function store(Request $request)
    {
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
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $det = ScheduleDetail::create($request->all());
        $det->load(['subject', 'teacher', 'room']);

        return response()->json(['success' => true, 'data' => $det], 201);
    }

    public function update(Request $request, $id)
    {
        $detail = ScheduleDetail::find($id);
        if (!$detail) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        $detail->update($request->all());
        $detail->load(['subject', 'teacher', 'room']);

        return response()->json(['success' => true, 'data' => $detail]);
    }

    public function destroy($id)
    {
        $detail = ScheduleDetail::find($id);
        if ($detail) {
            $detail->update(['IsDeleted' => 1]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }
}

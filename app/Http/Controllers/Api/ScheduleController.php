<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{

    public function index()
    {
        // Show All Schedules that are not deleted, with Academic Year and Class Section Info, Ordered by ScheduleID Descending
        $schedules = Schedule::with(['academicYear', 'classSection'])
            ->where('IsDeleted', 0)
            ->get();
        return response()->json(['success' => true, 'data' => $schedules]);
    }

    public function store(Request $request)
    {
        //  Update Validation to Use Laravel's Validator and Provide Clear Error Messages
        $validator = Validator::make($request->all(), [
        'YearID'    => 'required|exists:tblacademicyears,YearID',
        'SectionID' => 'required|exists:tblclasssections,SectionID',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    // create new schedule with IsDeleted default to 0
    $sch = Schedule::create([
        'YearID'    => $request->YearID,
        'SectionID' => $request->SectionID,
        'IsDeleted' => 0,
    ]);

    return response()->json(['success' => true, 'data' => $sch], 201);
    }

    public function show($id)
    {
        // Show Specific Schedule by ID with Academic Year, Class Section, and Schedule Details (Subject, Teacher, Room)
        $schedule = Schedule::with([
            'academicYear',
            'classSection',
            'details.subject',
            'details.teacher',
            'details.room'
        ])->find($id);

        if (!$schedule || $schedule->IsDeleted == 1) {
            return response()->json(['message' => 'Schedule not found or deleted'], 404);
        }

        return response()->json(['success' => true, 'data' => $schedule]);
    }

    public function update(Request $request, $id)
    {
        $sch = Schedule::find($id);
        if (!$sch) return response()->json(['message' => 'Schedule not found'], 404);

        $sch->update($request->all());
        return response()->json(['success' => true, 'data' => $sch]);
    }

    public function destroy($id)
    {
        $sch = Schedule::find($id);
        if (!$sch) return response()->json(['message' => 'Schedule not found'], 404);

        // ប្រើ Soft Delete
        $sch->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'Deleted schedule successfully']);
    }
}

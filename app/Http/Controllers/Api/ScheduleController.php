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
        // បង្ហាញកាលវិភាគជាមួយព័ត៌មានឆ្នាំសិក្សា និងថ្នាក់រៀន
        $schedules = Schedule::with(['academicYear', 'classSection'])
            ->where('IsDeleted', 0)
            ->get();
        return response()->json(['success' => true, 'data' => $schedules]);
    }

    public function store(Request $request)
    {
        //  កែឈ្មោះ Key ឱ្យត្រូវតាម Database និងអ្វីដែលអ្នកផ្ញើមកពី Postman
        $validator = Validator::make($request->all(), [
        'YearID'    => 'required|exists:tblacademicyears,YearID',
        'SectionID' => 'required|exists:tblclasssections,SectionID',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    // បង្កើតទិន្នន័យដោយកំណត់ IsDeleted ជា 0
    $sch = Schedule::create([
        'YearID'    => $request->YearID,
        'SectionID' => $request->SectionID,
        'IsDeleted' => 0,
    ]);

    return response()->json(['success' => true, 'data' => $sch], 201);
    }

    public function show($id)
    {
        // បង្ហាញលម្អិតកាលវិភាគ ព្រមទាំងម៉ោងសិក្សា មុខវិជ្ជា និងគ្រូដែលបង្រៀន
        $schedule = Schedule::with([
            'academicYear',
            'classSection',
            'details.subject',
            'details.teacher',
            'details.room'
        ])->find($id);

        if (!$schedule || $schedule->IsDeleted == 1) {
            return response()->json(['message' => 'រកមិនឃើញកាលវិភាគនេះទេ'], 404);
        }

        return response()->json(['success' => true, 'data' => $schedule]);
    }

    public function update(Request $request, $id)
    {
        $sch = Schedule::find($id);
        if (!$sch) return response()->json(['message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        $sch->update($request->all());
        return response()->json(['success' => true, 'data' => $sch]);
    }

    public function destroy($id)
    {
        $sch = Schedule::find($id);
        if (!$sch) return response()->json(['message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        // ប្រើ Soft Delete
        $sch->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'លុបកាលវិភាគជោគជ័យ']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Display a listing of schedules.
     * Accessible by both Students and Teachers.
     */
    public function index()
    {
    // Eager load details and their specific related info for the list view
    $schedules = Schedule::with([
        'academicYear', 
        'classSection', 
        'details' => function($query) {
            $query->where('IsDeleted', 0); 
        },
        'details.subject', 
        'details.teacher', 
        'details.room'
    ])
    ->where('IsDeleted', 0)
    ->orderBy('ScheduleID', 'desc')
    ->get();

    return response()->json([
        'success' => true, 
        'data'    => $schedules
    ], 200);
    }
    /**
     * Display the specified schedule with all details.
     */
    public function show($id)
    {
        $schedule = Schedule::with([
            'academicYear',
            'classSection',
            'details' => function($query) {
                $query->where('IsDeleted', 0);
            },
            'details.subject',
            'details.teacher',
            'details.room'
        ])->find($id);

        if (!$schedule || $schedule->IsDeleted == 1) {
            return response()->json(['success' => false, 'message' => 'Schedule not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $schedule], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | PROTECTED METHODS (Blocked for Students)
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        // Check Role
        if (Auth::user()->Role === 'Student') {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Students cannot create schedules'], 403);
        }

        $validator = Validator::make($request->all(), [
            'YearID'    => 'required|exists:tblacademicyears,YearID',
            'SectionID' => 'required|exists:tblclasssections,SectionID',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $sch = Schedule::firstOrCreate(
            ['YearID' => $request->YearID, 'SectionID' => $request->SectionID],
            ['IsDeleted' => 0]
        );

        if ($sch->IsDeleted == 1) {
            $sch->update(['IsDeleted' => 0]);
        }

        return response()->json(['success' => true, 'data' => $sch], 201);
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->Role === 'Student') {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Students cannot update schedules'], 403);
        }

        $sch = Schedule::where('IsDeleted', 0)->find($id);
        if (!$sch) return response()->json(['success' => false, 'message' => 'Schedule not found'], 404);

        $sch->update($request->only(['YearID', 'SectionID']));
        
        return response()->json(['success' => true, 'data' => $sch]);
    }

    public function destroy($id)
    {
        if (Auth::user()->Role === 'Student') {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Students cannot delete schedules'], 403);
        }

        $sch = Schedule::find($id);
        if (!$sch) return response()->json(['success' => false, 'message' => 'Schedule not found'], 404);

        $sch->update(['IsDeleted' => 1]);
        
        return response()->json(['success' => true, 'message' => 'Schedule removed successfully']);
    }
}
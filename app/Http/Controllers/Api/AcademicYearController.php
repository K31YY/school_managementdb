<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of academic years (Excluding soft-deleted records).
     */
    public function index()
    {
        try {
            $years = AcademicYear::where('IsDeleted', 0)
                ->orderBy('YearID', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data'    => $years
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch academic years',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created academic year in tblacademicyears.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'YearName'    => 'required|string|unique:tblacademicyears,YearName',
            'StartDate'   => 'required|date',
            'EndDate'     => 'required|date|after:StartDate',
            'Description' => 'nullable|string',
        ], [
            'YearName.unique' => 'This Academic Year already exists.',
            'EndDate.after'   => 'The End Date must be a date after the Start Date.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $year = AcademicYear::create([
                'YearName'    => $request->YearName,
                'StartDate'   => $request->StartDate,
                'EndDate'     => $request->EndDate,
                'Description' => $request->Description,
                'IsDeleted'   => 0, // Default to active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Academic Year created successfully',
                'data'    => $year
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified academic year.
     */
    public function show($id)
    {
        $year = AcademicYear::where('YearID', $id)
            ->where('IsDeleted', 0)
            ->first();

        if (!$year) {
            return response()->json([
                'success' => false,
                'message' => 'Academic Year not found or has been deleted'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $year
        ], 200);
    }

    /**
     * Update the specified academic year.
     */
    public function update(Request $request, $id)
    {
        $year = AcademicYear::find($id);

        if (!$year) {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'YearName'    => 'required|string|unique:tblacademicyears,YearName,' . $id . ',YearID',
            'StartDate'   => 'required|date',
            'EndDate'     => 'required|date|after:StartDate',
            'Description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $year->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Academic Year updated successfully',
            'data'    => $year
        ], 200);
    }

    /**
     * Soft delete the academic year by setting IsDeleted to 1.
     */
    public function destroy($id)
    {
    // Use where with YearID to be 100% sure Laravel finds it
    $year = AcademicYear::where('YearID', $id)->first();

    if (!$year) {
        return response()->json([
            'success' => false,
            'message' => 'Academic Year not found with ID: ' . $id
        ], 404);
    }

    if ($year->IsDeleted == 1) {
        return response()->json([
            'success' => false,
            'message' => 'This record is already deleted'
        ], 400);
    }

    // Perform the soft delete
    $year->update(['IsDeleted' => 1]);

    return response()->json([
        'success' => true,
        'message' => 'Deleted successfully'
    ], 200);
    }
}
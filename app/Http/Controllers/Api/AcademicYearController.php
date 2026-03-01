<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicYearController extends Controller
{
    // Show All Academic Years (Exclude Deleted Ones and Order by YearID Descending)
    public function index()
    {
        return response()->json([
            'success' => true,
            // Update to use Eloquent ORM for better readability and maintainability
            'data' => AcademicYear::where('IsDeleted', 0)
                ->orderBy('YearID', 'desc')
                ->get()
        ]);
    }

    // Add New Academic Year
    public function store(Request $request)
    {
        // Update Validation to Use Laravel's Validator and Provide Clear Error Messages
        $validator = Validator::make($request->all(), [
            'YearName'  => 'required|unique:tblacademicyears,YearName',
            'StartDate' => 'required|date',
            'EndDate'   => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'errors'  => $validator->errors()
            ], 422);
        }

        // Insert New Academic Year with IsDeleted Default to 0
        $data = AcademicYear::create([
            'YearName'    => $request->YearName,
            'StartDate'   => $request->StartDate,
            'EndDate'     => $request->EndDate,
            'Description' => $request->Description,
            'IsDeleted'   => 0,
        ]);

        return response()->json([
            'success' => true, 
            'data'    => $data
        ], 201);
    }

    // Show Specific Academic Year by ID
    public function show($id)
    {
        $year = AcademicYear::find($id);
        if (!$year || $year->IsDeleted == 1) {
            return response()->json(['message' => 'Academic year not found or deleted'], 404);
        }
        return response()->json(['success' => true, 'data' => $year]);
    }

    // Update Academic Year by ID
    public function update(Request $request, $id)
    {
        $year = AcademicYear::find($id);
        if (!$year) {
            return response()->json(['message' => 'Academic year not found'], 404);
        }

        // Can add validation here if needed, similar to the store method
        $year->update($request->all());

        return response()->json([
            'success' => true, 
            'data'    => $year
        ]);
    }

    // Delete Academic Year (Soft Delete by Setting IsDeleted to 1)
    public function destroy($id)
    {
        $year = AcademicYear::find($id);
        if (!$year) {
            return response()->json(['message' => 'Academic year not found'], 404);
        }

        // Change IsDeleted to 1 Instead of Removing the Record from the Database
        $year->update(['IsDeleted' => 1]);

        return response()->json([
            'success' => true, 
            'message' => 'Deleted academic year successfully'
        ]);
    }
}

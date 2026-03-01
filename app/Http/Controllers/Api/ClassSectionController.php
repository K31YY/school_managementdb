<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassSectionController extends Controller
{
    public function index()
    {
        // Get All Sections with Academic Year, Exclude Deleted Ones, and Order by SectionID Descending
        $sections = ClassSection::with('academicYear')
            ->where('IsDeleted', 0)
            ->orderBy('SectionID', 'desc')
            ->get();
            
        return response()->json(['success' => true, 'data' => $sections]);
    }

    public function store(Request $request)
    {
        // Use Validator to Ensure Required Fields are Present and Valid
        $validator = Validator::make($request->all(), [
            'SectionName' => 'required|string|max:255',
            'YearID'      => 'required|exists:tblacademicyears,YearID',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Insert New Section with IsDeleted Default to 0
        $sec = ClassSection::create([
            'SectionName' => $request->SectionName,
            'YearID'      => $request->YearID,
            'IsDeleted'   => 0 
        ]);

        return response()->json(['success' => true, 'data' => $sec], 201);
    }

    public function show($id)
    {
        $section = ClassSection::with('academicYear')->find($id);

        if (!$section || $section->IsDeleted == 1) {
            return response()->json(['success' => false, 'message' => 'Section not found or deleted'], 404);
        }
        return response()->json(['success' => true, 'data' => $section]);
    }

    public function update(Request $request, $id)
    {
        $section = ClassSection::find($id);
        if (!$section) return response()->json(['success' => false, 'message' => 'Section not found'], 404);

        // Update Section with New Data from Request
        $section->update($request->all());
        return response()->json(['success' => true, 'data' => $section]);
    }

    public function destroy($id)
    {
        $section = ClassSection::find($id);
        if (!$section) return response()->json(['success' => false, 'message' => 'Section not found'], 404);

        // Soft Delete by Setting IsDeleted to 1 Instead of Removing the Record
        $section->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'Deleted section successfully']);
    }
}

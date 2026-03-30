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
        // Added with('academicYear') to ensure Flutter can display the year name
        $sections = ClassSection::with('academicYear')
            ->where('IsDeleted', 0)
            ->orderBy('SectionID', 'desc')
            ->get();
            
        return response()->json(['success' => true, 'data' => $sections]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'SectionName' => 'required|string|max:255',
            'YearID'      => 'required|exists:tblacademicyears,YearID',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Using create with explicit values is safer than $request->all()
        $sec = ClassSection::create([
            'SectionName' => $request->SectionName,
            'YearID'      => $request->YearID,
            'IsDeleted'   => 0 
        ]);

        return response()->json(['success' => true, 'data' => $sec], 201);
    }

    public function update(Request $request, $id)
    {
        $section = ClassSection::where('IsDeleted', 0)->find($id);
        
        if (!$section) {
            return response()->json(['success' => false, 'message' => 'Section not found'], 404);
        }

        // VALIDATION: You should also validate updates!
        $validator = Validator::make($request->all(), [
            'SectionName' => 'sometimes|string|max:255',
            'YearID'      => 'sometimes|exists:tblacademicyears,YearID',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Only update allowed fields, preventing someone from changing 'IsDeleted' here
        $section->update($request->only(['SectionName', 'YearID']));
        
        return response()->json(['success' => true, 'data' => $section]);
    }

    public function destroy($id)
    {
        $section = ClassSection::find($id);
        if (!$section) return response()->json(['success' => false, 'message' => 'Section not found'], 404);

        // This is exactly right for soft deletes
        $section->update(['IsDeleted' => 1]); 
        
        return response()->json(['success' => true, 'message' => 'Deleted section successfully']);
    }
}
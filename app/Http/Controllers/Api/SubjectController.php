<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Subject::where('IsDeleted', 0)->orderBy('SubID', 'desc')->get()
        ]);
    }

    public function store(Request $request)
    {
        // Update Validation to Use Laravel's Validator and Provide Clear Error Messages
        $validator = Validator::make($request->all(), [
            'SubName' => 'required|unique:tblsubjects,SubName',
            'Level'   => 'required',
            'Credit'  => 'nullable|numeric',
            'Hour'    => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Insert New Subject with IsDeleted Default to 0
        $sub = Subject::create([
            'SubName'     => $request->SubName,
            'Level'       => $request->Level,
            'Credit'      => $request->Credit,
            'Hour'        => $request->Hour,
            'Description' => $request->Description,
            'IsDeleted'   => 0,
        ]);

        return response()->json(['success' => true, 'data' => $sub], 201);
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::find($id);
        if (!$subject) return response()->json(['message' => 'Subject not found'], 404);

        // can add validation here if needed, similar to store method
        $subject->update($request->all());
        
        return response()->json(['success' => true, 'data' => $subject]);
    }

    public function destroy($id)
    {
        $subject = Subject::find($id);
        if (!$subject) return response()->json(['message' => 'Subject not found'], 404);

        // ប្រើ Soft Delete ដោយប្ដូរ IsDeleted ទៅជា ១
        $subject->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'Deleted subject data successfully']);
    }
}
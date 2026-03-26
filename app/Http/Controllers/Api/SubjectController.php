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
            // Correctly filters active subjects
            'data' => Subject::where('IsDeleted', 0)->orderBy('SubID', 'desc')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'SubName' => 'required|unique:tblsubjects,SubName',
            'Level'   => 'required',
            'Credit'  => 'nullable|numeric',
            'Hour'    => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Mass assignment works now that IsDeleted is added to Model
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

        // CRITICAL FIX: Validation for Update
        // This allows the current record to keep its name but prevents duplicates with others
        $validator = Validator::make($request->all(), [
            'SubName' => 'sometimes|required|unique:tblsubjects,SubName,'.$id.',SubID',
            'Level'   => 'sometimes|required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $subject->update($request->all());
        
        return response()->json(['success' => true, 'data' => $subject]);
    }

    public function destroy($id)
    {
        // find($id) works because primaryKey is set to SubID in the Model
        $subject = Subject::find($id);
        if (!$subject) return response()->json(['message' => 'Subject not found'], 404);

        // Correct soft-delete logic
        $subject->update(['IsDeleted' => 1]); 
        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }
}
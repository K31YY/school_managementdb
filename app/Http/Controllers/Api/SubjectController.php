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
        // កែសម្រួល Validation ឱ្យប្រើ Key "SubName"
        $validator = Validator::make($request->all(), [
            'SubName' => 'required|unique:tblsubjects,SubName',
            'Level'   => 'required',
            'Credit'  => 'nullable|numeric',
            'Hour'    => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // បញ្ចូលទិន្នន័យដោយប្រើ Key ត្រឹមត្រូវតាម Database
        $sub = Subject::create([
            'SubName'     => $request->SubName,
            'Level'       => $request->Level,
            'Credit'      => $request->Credit,
            'Hour'        => $request->Hour,
            'Description' => $request->Description,
            'IsDeleted'   => 0, // កំណត់តម្លៃដើម
        ]);

        return response()->json(['success' => true, 'data' => $sub], 201);
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::find($id);
        if (!$subject) return response()->json(['message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        // អនុញ្ញាតឱ្យ Update គ្រប់ Field ដែលមានក្នុង fillable
        $subject->update($request->all());
        
        return response()->json(['success' => true, 'data' => $subject]);
    }

    public function destroy($id)
    {
        $subject = Subject::find($id);
        if (!$subject) return response()->json(['message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        // ប្រើ Soft Delete ដោយប្ដូរ IsDeleted ទៅជា ១
        $subject->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'លុបមុខវិជ្ជាជោគជ័យ']);
    }
}
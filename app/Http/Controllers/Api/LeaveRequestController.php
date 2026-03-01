<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeaveRequestController extends Controller
{

    public function index()
    {
        // បង្ហាញសំណើសុំច្បាប់ទាំងអស់ ព្រមជាមួយព័ត៌មានសិស្ស
        $requests = LeaveRequest::with(['student'])->orderBy('CreatedDate', 'desc')->get();
        return response()->json(['success' => true, 'data' => $requests]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'StudentID' => 'required|exists:tblstudents,StudentID',
            'StartDate' => 'required|date',
            'EndDate'   => 'required|date|after_or_equal:StartDate',
            'Reason'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // បន្ថែម Status 'Pending' ជាលំនាំដើម ប្រសិនបើមិនទាន់មាន
        $data = $request->all();
        if (!isset($data['Status'])) {
            $data['Status'] = 'Pending';
        }

        $leave = LeaveRequest::create($data);
        return response()->json(['success' => true, 'data' => $leave], 201);
    }

    public function show($id)
    {
        $leave = LeaveRequest::with(['student'])->find($id);
        if (!$leave) return response()->json(['message' => 'រកមិនឃើញសំណើសុំច្បាប់នេះទេ'], 404);

        return response()->json(['success' => true, 'data' => $leave]);
    }

    public function update(Request $request, $id)
    {
        $leave = LeaveRequest::find($id);
        if (!$leave) return response()->json(['message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        // ប្រើសម្រាប់គ្រូ ឬ Admin ដើម្បី Approve/Reject
        $leave->update($request->all());
        return response()->json(['success' => true, 'data' => $leave]);
    }

    public function destroy($id)
    {
        $leave = LeaveRequest::find($id);
        if (!$leave) return response()->json(['message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        $leave->delete();
        return response()->json(['success' => true, 'message' => 'លុបសំណើសុំច្បាប់ជោគជ័យ']);
    }
}

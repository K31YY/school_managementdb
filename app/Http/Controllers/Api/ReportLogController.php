<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportLogController extends Controller
{

    public function index()
    {
        // Show All Report Logs with User Info, Ordered by GeneratedAt Descending
        $logs = ReportLog::with('user')->latest('GeneratedAt')->get();
        return response()->json(['success' => true, 'data' => $logs]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UserID' => 'required|exists:tblusers,UserID',
            'ReportType' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $log = ReportLog::create([
            'UserID' => $request->UserID,
            'ReportType' => $request->ReportType,
            'GeneratedAt' => now()
        ]);

        return response()->json(['success' => true, 'data' => $log], 201);
    }

    public function show($id)
    {
        $log = ReportLog::with('user')->find($id);
        if (!$log) return response()->json(['message' => 'Report log not found'], 404);

        return response()->json(['success' => true, 'data' => $log]);
    }

    public function destroy($id)
    {
        $log = ReportLog::find($id);
        if (!$log) return response()->json(['message' => 'Report log not found'], 404);

        $log->delete();
        return response()->json(['success' => true, 'message' => 'Deleted report log successfully']);
    }
}

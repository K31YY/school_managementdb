<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    public function index()
    {
        // Return only active rooms, ordered by ID
        $rooms = Room::where('IsDeleted', 0)
            ->orderBy('RoomID', 'desc')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    public function store(Request $request)
    {
        // Validate specifically for tblrooms column names
        $validator = Validator::make($request->all(), [
            'RoomName' => 'required|unique:tblrooms,RoomName',
            'Capacity' => 'nullable|integer',
            'Location' => 'nullable|string',
            'Status'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Mass assignment is cleaner if $fillable is set in the Model
        $room = Room::create([
            'RoomName'  => $request->RoomName,
            'Location'  => $request->Location,
            'Capacity'  => $request->Capacity ?? 0,
            'Status'    => $request->Status ?? 'Available',
            'IsDeleted' => 0,
        ]);
        return response()->json(['success' => true, 'data' => $room], 201);
    }
    public function show($id)
    {
        $room = Room::where('RoomID', $id)->where('IsDeleted', 0)->first();
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Room not found or deleted'], 404);
        }
        return response()->json(['success' => true, 'data' => $room]);
    }

    public function update(Request $request, $id)
    {
        $room = Room::find($id);
        if (!$room || $room->IsDeleted == 1) {
            return response()->json(['success' => false, 'message' => 'Room not found'], 404);
        }
        // CRITICAL FIX: You MUST validate update data too.

        // Otherwise, someone could accidentally change RoomName to a duplicate.

        $validator = Validator::make($request->all(), [
            'RoomName' => 'sometimes|required|unique:tblrooms,RoomName,' . $id . ',RoomID',
            'Capacity' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        // Use only specific fields to prevent users from injecting 'IsDeleted' => 0 via update
        $room->update($request->only(['RoomName', 'Location', 'Capacity', 'Status']));
        return response()->json(['success' => true, 'data' => $room]);
    }
    public function destroy($id)
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Room not found'], 404);
        }
        // Soft delete: keep the record but hide it from the app
        $room->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'Deleted room successfully']);
    }
}
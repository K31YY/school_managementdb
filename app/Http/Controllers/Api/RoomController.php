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
        // Show All Rooms that are not deleted, Ordered by RoomID Descending
        return response()->json([
            'success' => true,
            'data' => Room::where('IsDeleted', 0)->orderBy('RoomID', 'desc')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'RoomName' => 'required|unique:tblrooms,RoomName',
            'Capacity' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Make sure to set default values for Status and IsDeleted if they are not provided in the request
        $room = Room::create([
            'RoomName' => $request->RoomName,
            'Location' => $request->Location,
            'Capacity' => $request->Capacity,
            'Status'   => $request->Status ?? 'Available', 
            'IsDeleted' => 0,
        ]);

        return response()->json(['success' => true, 'data' => $room], 201);
    }

    public function show($id)
    {
        $room = Room::find($id);
        // Check if the room exists and is not marked as deleted (IsDeleted = 1) before returning it
        if (!$room || $room->IsDeleted == 1) {
            return response()->json(['message' => 'Room not found or deleted'], 404);
        }
        return response()->json(['success' => true, 'data' => $room]);
    }

    public function update(Request $request, $id)
    {
        $room = Room::find($id);
        if (!$room) return response()->json(['message' => 'Room not found'], 404);

        $room->update($request->all());
        return response()->json(['success' => true, 'data' => $room]);
    }

    public function destroy($id)
    {
        $room = Room::find($id);
        if (!$room) return response()->json(['message' => 'Room not found'], 404);

        // Update the IsDeleted field to 1 instead of actually deleting the record from the database
        $room->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'Deleted room successfully']);
    }
}

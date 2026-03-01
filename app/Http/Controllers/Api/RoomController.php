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
        // បង្ហាញតែបន្ទប់ដែលមិនទាន់លុប និងតម្រៀបតាម ID
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

        // បង្កើតទិន្នន័យដោយកំណត់ IsDeleted ជា 0 ជា Default
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
        // ឆែកមើល RoomID និងស្ថានភាព IsDeleted
        if (!$room || $room->IsDeleted == 1) {
            return response()->json(['message' => 'រកមិនឃើញបន្ទប់នេះទេ'], 404);
        }
        return response()->json(['success' => true, 'data' => $room]);
    }

    public function update(Request $request, $id)
    {
        $room = Room::find($id);
        if (!$room) return response()->json(['message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        $room->update($request->all());
        return response()->json(['success' => true, 'data' => $room]);
    }

    public function destroy($id)
    {
        $room = Room::find($id);
        if (!$room) return response()->json(['message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        // កែប្រែ IsDeleted ទៅជា 1 តាមរចនាសម្ព័ន្ធ Table
        $room->update(['IsDeleted' => 1]);
        return response()->json(['success' => true, 'message' => 'លុបបន្ទប់ជោគជ័យ']);
    }
}

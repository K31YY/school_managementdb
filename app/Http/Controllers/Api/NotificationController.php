<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{

    public function index(Request $request)
    {
        // បង្ហាញការជូនដំណឹងដែលថ្មីៗបំផុត និងអាច Filter តាម UserID
        $query = Notification::latest();

        if ($request->has('UserID')) {
            $query->where('UserID', $request->UserID);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UserID'  => 'required|exists:tblusers,UserID',
            'Title'   => 'required',
            'Message' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $not = Notification::create(array_merge($request->all(), ['IsRead' => 0]));
        return response()->json(['success' => true, 'data' => $not], 201);
    }

    public function show($id)
    {
        $notification = Notification::find($id);
        if (!$notification) return response()->json(['message' => 'រកមិនឃើញទិន្នន័យ'], 404);

        // នៅពេល User ចុចមើល យើងអាចប្តូរវាទៅជា "អានរួច" អូតូតែម្តង
        $notification->update(['IsRead' => 1]);

        return response()->json(['success' => true, 'data' => $notification]);
    }

    public function update(Request $request, $id)
    {
        // ប្រើសម្រាប់ប្តូរ Status IsRead ដោយឡែក
        $not = Notification::findOrFail($id);
        $not->update(['IsRead' => $request->IsRead]);
        return response()->json(['success' => true, 'data' => $not]);
    }

    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'លុបការជូនដំណឹងជោគជ័យ']);
    }
}

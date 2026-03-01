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
        // Show All Notifications, Optionally Filter by UserID, Ordered by CreatedDate Descending
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
        if (!$notification) return response()->json(['message' => 'Notification not found'], 404);

        // When a notification is viewed, we can mark it as read by updating the IsRead field to 1
        $notification->update(['IsRead' => 1]);

        return response()->json(['success' => true, 'data' => $notification]);
    }

    public function update(Request $request, $id)
    {
        // Use for updating the IsRead status of a notification, for example, to mark it as read or unread
        $not = Notification::findOrFail($id);
        $not->update(['IsRead' => $request->IsRead]);
        return response()->json(['success' => true, 'data' => $not]);
    }

    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Deleted notification successfully']);
    }
}

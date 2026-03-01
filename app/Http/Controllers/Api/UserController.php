<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index()
    {
        return response()->json(['success' => true, 'data' => User::all()]);
    }

    public function store(Request $request)
    {
        // Add Validation for Required Fields and Unique Username
        $validator = Validator::make($request->all(), [
            'Username' => 'required|unique:tblusers,Username',
            'Password' => 'required|min:6',
            'Role' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'Username' => $request->Username,
            'Password' => Hash::make($request->Password),
            'Role' => $request->Role,
            'Status' => $request->Status ?? 1
        ]);

        return response()->json(['success' => true, 'data' => $user], 201);
    }

    public function show($id)
    {
        // Use UserID to Find User and Include Student and Teacher Relations, Check if User Exists Before Returning
        $user = User::with(['student', 'teacher'])->find($id);
        return $user ? response()->json(['success' => true, 'data' => $user])
            : response()->json(['message' => 'User not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        if ($request->Password) {
            $user->Password = Hash::make($request->Password);
        }

        $user->update($request->only(['Username', 'Role', 'Status']));
        return response()->json(['success' => true, 'data' => $user]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $user->delete();
        return response()->json(['success' => true, 'message' => 'Deleted user data successfully']);
    }
}

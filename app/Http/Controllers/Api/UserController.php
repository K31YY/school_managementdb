<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        return response()->json([
            'success' => true, 
            'data' => User::all()
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Username' => 'required|unique:tblusers,Username',
            'Password' => 'required|min:6',
            'Role'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'Username' => $request->Username,
            'Password' => Hash::make($request->Password),
            'Role'     => $request->Role,
            'Status'   => $request->Status ?? 1
        ]);

        return response()->json([
            'success' => true, 
            'data'    => $user
        ], 201);
    }

    /**
     * Display the specified user with relationships.
     */
    public function show($id)
    {
        // Rigorous Check: Include relationships only if they exist in the Model
        $user = User::with(['student', 'teacher'])->find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false, 
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true, 
            'data'    => $user
        ]);
    }

    /**
     * Update the specified user (Admin Reset/Edit).
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false, 
                'message' => 'User not found'
            ], 404);
        }

        // Logic: Validate inputs. 'sometimes' allows updating only specific fields.
        // We ignore the current user's ID for the unique Username check.
        $validator = Validator::make($request->all(), [
            'Username' => 'sometimes|unique:tblusers,Username,' . $id . ',UserID',
            'Password' => 'sometimes|min:6',
            'Role'     => 'sometimes',
            'Status'   => 'sometimes|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'errors'  => $validator->errors()
            ], 422);
        }

        // Analytical Fix: Gather only allowed data
        $data = $request->only(['Username', 'Role', 'Status']);

        // Rigorous Check: Only hash and update password if it's actually provided
        if ($request->filled('Password')) {
            $data['Password'] = Hash::make($request->Password);
        }

        $user->update($data);

        return response()->json([
            'success' => true, 
            'data'    => $user,
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false, 
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();
        
        return response()->json([
            'success' => true, 
            'message' => 'Deleted user data successfully'
        ]);
    }
}
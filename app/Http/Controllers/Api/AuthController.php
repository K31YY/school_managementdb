<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        // Validation for Username and Password
        $validator = Validator::make($request->all(), [
            'Username' => 'required',
            'Password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Please Input  Username and Password'], 400);
        }

        // Find User by Username
        $user = User::where('Username', $request->Username)->first();

        // ៣. Matching Passsword
        if (!$user || !Hash::check($request->Password, $user->Password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username or Password Not Match'
            ], 401);
        }

        // Create Token for API Authentication
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Successfully',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Logout Successfully']);
    }
}

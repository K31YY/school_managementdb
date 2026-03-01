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
        // ១. ធ្វើ Validation ដើម្បីធានាថាមានទិន្នន័យផ្ញើមកពិតមែន
        $validator = Validator::make($request->all(), [
            'Username' => 'required',
            'Password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'សូមបញ្ចូល Username និង Password'], 400);
        }

        // ២. ស្វែងរក User តាមរយៈ Username
        $user = User::where('Username', $request->Username)->first();

        // ៣. ផ្ទៀងផ្ទាត់ User និង Password
        if (!$user || !Hash::check($request->Password, $user->Password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username ឬ Password មិនត្រឹមត្រូវទេ'
            ], 401);
        }

        // ៤. បង្កើត Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'ចូលប្រើប្រាស់ជោគជ័យ',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        // លុប Token បច្ចុប្បន្នចោល
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'ចាកចេញជោគជ័យ']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $field = $request->validate(['email' => 'required|email', 'password' => 'required|string']);

        // Check email
        $user = User::where('email', $field['email'])->first();

        // Check password
        if (!$user || !password_verify($request->password, $user->password)) {
            return response([
                'success' => false,
                'status' => 'FAILED_AUTH',
                'message' => 'Bad credentials!'
            ], 401);
        }

        //Create Token
        $token = $user->createToken('logintoken')->plainTextToken;

        return response()->json(['token' => $token], 201);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return ['message' => 'logget out'];
    }
}

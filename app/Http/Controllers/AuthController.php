<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register(StoreUserRequest $request)
    {
        $user = User::create(
            array_merge(
                $request->safe()->except(['password']),
                ['password' => bcrypt('password')]
            )
        );

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

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

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return ['message' => 'logget out'];
    }
}

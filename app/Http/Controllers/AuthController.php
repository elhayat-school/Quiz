<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
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

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        $field = $request->validate([
            'email' => 'required',
            'password' => 'required|string'
        ]);
        // Check email

        $user = User::where('email', $field['email'])->first();

        // Check password

        if(!$user || ! Hash::check('password', $user->password))
        {
            return response([
                'success' => false,
                'status' => 'FAILED_AUTH',
                'message' => 'Bad creds'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'logget out'
        ];
    }
}

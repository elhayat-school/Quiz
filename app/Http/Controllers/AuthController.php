<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

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

    public function login()
    {
        //
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'logget out'
        ];
    }
}

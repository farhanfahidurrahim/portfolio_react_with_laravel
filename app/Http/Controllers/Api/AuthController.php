<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try{
            if(Auth::attempt($request->only('email','password'))){
                $user = Auth::user();
                $token = $user->createToken('app')->accessToken;

                return response()->json([
                    'message' => "Successfully Login!",
                    'token' => $token,
                    'user' => $user,
                ],200);
            }

        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
            ],400);
        }

        return response()->json([
            'message' => 'Invalid Email / Password!'
        ],401);
    }

    public function register(RegisterRequest $request)
    {
        try{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('app')->accessToken;

            return response()->json([
                'message' => 'Registration Successfully!',
                'token' => $token,
                'user' => $user,
            ],200);

        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
            ],400);
        }

    }
}

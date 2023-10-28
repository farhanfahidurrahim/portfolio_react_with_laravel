<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Mail\ForgetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

    public function forgetPassword(ForgetRequest $request)
    {
        $email = $request->email;
        //Email Check
        if (User::where('email',$email)->doesntExist()) {
            return response([
                'message' => 'Email Invalid',
            ],401);
        }

        //if Email Valid
        $token = rand(10,10000);
        try{
            DB::table('password_reset_tokens')->insert([
                'email' => $email,
                'token' => $token,
            ]);

            //Mail Send to User with Token
            Mail::to($email)->send(new ForgetMail($token));

            return response([
                'message' => 'Reset Password Mail Send Your Email!',
            ],200);

        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
            ],400);
        }

    }
}
